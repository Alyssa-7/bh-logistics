<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::with('deliveryOrder:id,do_number')
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($n) => array_merge($n->toArray(), [
                'do_number' => $n->deliveryOrder?->do_number,
            ]));

        return response()->json($notifications);
    }

    public function markRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'อ่านแล้ว']);
    }
}
