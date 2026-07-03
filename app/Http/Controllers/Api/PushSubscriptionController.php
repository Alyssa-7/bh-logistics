<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function vapidPublicKey()
    {
        return response()->json(['key' => config('webpush.vapid.public_key')]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'p256dh'   => 'required|string',
            'auth'     => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id'    => $request->user()->id,
                'p256dh_key' => $request->p256dh,
                'auth_token' => $request->auth,
            ]
        );

        return response()->json(['message' => 'subscribed']);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'unsubscribed']);
    }

    public function testPush(Request $request)
    {
        $push = app(\App\Services\PushNotificationService::class);
        $push->sendToUsers([$request->user()->id], 'BH-Logistic ทดสอบ', 'การแจ้งเตือนนอกแอปทำงานปกติ ✅', '/');

        return response()->json(['message' => 'ส่ง push แล้ว']);
    }
}
