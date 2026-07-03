<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        $total = LoginHistory::count();

        $histories = LoginHistory::with('user:id,name,email,role')
            ->latest('created_at')
            ->limit(300)
            ->get()
            ->map(fn ($h) => [
                'id'         => $h->id,
                'user_id'    => $h->user_id,
                'name'       => $h->user?->name  ?? '(ลบแล้ว)',
                'email'      => $h->user?->email ?? '-',
                'role'       => $h->user?->role  ?? 'unknown',
                'action'     => $h->action,
                'ip_address' => $h->ip_address ?? '-',
                'device'     => $this->detectDevice($h->user_agent),
                'created_at' => $h->created_at?->toDateTimeString(),
            ]);

        return response()->json([
            'total' => $total,
            'data'  => $histories,
        ]);
    }

    private function detectDevice(?string $ua): string
    {
        if (!$ua) return 'ไม่ทราบ';
        $ua = strtolower($ua);
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'android') || str_contains($ua, 'mobile')) {
            if (str_contains($ua, 'iphone'))  return 'iPhone';
            if (str_contains($ua, 'ipad'))    return 'iPad';
            if (str_contains($ua, 'android')) return 'Android';
            return 'มือถือ';
        }
        return 'คอมพิวเตอร์';
    }
}
