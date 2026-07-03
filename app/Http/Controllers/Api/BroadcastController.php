<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BroadcastMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message'      => 'required|string|max:500',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:admin,hq,driver,branch,all',
            'priority'     => 'in:normal,important,urgent',
        ]);

        $roles = $request->target_roles;
        if (in_array('all', $roles)) {
            $roles = ['admin', 'hq', 'driver', 'branch'];
        }

        $users = User::whereIn('role', $roles)->where('is_active', true)->get();

        $now = now();
        $insertData = $users->map(fn ($u) => [
            'user_id'    => $u->id,
            'type'       => 'broadcast',
            'do_id'      => null,
            'message'    => $request->message,
            'is_read'    => false,
            'created_at' => $now,
        ])->toArray();

        if (!empty($insertData)) {
            Notification::insert($insertData);
        }

        $targetLabel = implode(',', $request->target_roles);

        BroadcastMessage::create([
            'message'         => $request->message,
            'target_roles'    => $targetLabel,
            'priority'        => $request->priority ?? 'normal',
            'recipient_count' => $users->count(),
            'sender_id'       => $request->user()->id,
        ]);

        return response()->json([
            'message'   => 'ส่งแจ้งเตือนเรียบร้อย',
            'sent_to'   => $users->count(),
        ]);
    }

    public function history()
    {
        $rows = BroadcastMessage::with('sender:id,name')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($b) => [
                'id'              => $b->id,
                'message'         => $b->message,
                'target_roles'    => $b->target_roles,
                'priority'        => $b->priority,
                'recipient_count' => $b->recipient_count,
                'sender_name'     => $b->sender?->name ?? '-',
                'created_at'      => $b->created_at?->toDateTimeString(),
            ]);

        return response()->json($rows);
    }
}
