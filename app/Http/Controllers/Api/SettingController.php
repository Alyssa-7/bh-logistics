<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $rows = Setting::all()->keyBy('key')->map(fn($r) => $r->value);
        return response()->json($rows);
    }

    public function update(Request $request)
    {
        // Extended settings (no required validation — save only if present)
        $extKeys = ['system_name', 'login_max_attempts', 'session_timeout_minutes',
                    'password_change_days', 'logo_base64'];
        foreach ($extKeys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        // HQ location — only validate/save when explicitly provided
        if ($request->has('hq_lat') || $request->has('hq_lng')) {
            $request->validate([
                'hq_lat'  => 'required|numeric|between:-90,90',
                'hq_lng'  => 'required|numeric|between:-180,180',
                'hq_name' => 'nullable|string|max:100',
            ]);
            Setting::set('hq_lat',  $request->hq_lat);
            Setting::set('hq_lng',  $request->hq_lng);
            Setting::set('hq_name', $request->hq_name ?? 'คลังสินค้ากลาง (HQ)');
        }

        return response()->json(['message' => 'บันทึกการตั้งค่าเรียบร้อย']);
    }
}
