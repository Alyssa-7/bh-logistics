<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Account is inactive.'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        LoginHistory::create([
            'user_id'    => $user->id,
            'action'     => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => $user->load('branch'),
        ]);
    }

    public function logout(Request $request)
    {
        LoginHistory::create([
            'user_id'    => $request->user()->id,
            'action'     => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('branch'));
    }
public function employeeLogin(Request $request)
{
    $request->validate([
        'employee_code' => 'required|string',
        'password'      => 'required|string',
    ]);

    $user = User::where('employee_code', $request->employee_code)
                ->where('is_active', true)
                ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'รหัสพนักงานหรือรหัสผ่านไม่ถูกต้อง'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    LoginHistory::create([
        'user_id'    => $user->id,
        'action'     => 'login',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    return response()->json([
        'token' => $token,
        'user'  => $user->load('branch'),
    ]);
}

public function quickAccess(Request $request)
{
    $request->validate(['role' => 'required|in:admin,hq,driver,branch']);

    $user = User::where('role', $request->role)->where('is_active', true)->first();

    if (!$user) {
        return response()->json(['message' => 'ไม่พบผู้ใช้สำหรับ role นี้'], 404);
    }

    $token = $user->createToken('quick-access')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => $user->load('branch'),
    ]);
}

public function pinLogin(Request $request)
{
    $roleMap = [
        'ครัวกลาง'  => 'hq',
        'ไรเดอร์'   => 'driver',
        'หน้าสาขา' => 'branch',
        'แอดมิน'   => 'admin',
    ];

    $request->validate([
        'role' => 'required|string',
        'pin'  => 'required|digits:4',
    ]);

    $dbRole = $roleMap[$request->role] ?? null;
    if (!$dbRole) {
        return response()->json(['message' => 'ประเภทผู้ใช้ไม่ถูกต้อง'], 422);
    }

    $query = User::where('role', $dbRole)->where('is_active', true)->whereNotNull('pin');
    if ($request->filled('employee_code')) {
        $query->where('employee_code', $request->employee_code);
    }
    $users = $query->get();

    $matched = $users->first(fn($u) => Hash::check($request->pin, $u->pin));

    if (!$matched) {
        return response()->json(['message' => 'PIN ไม่ถูกต้อง'], 401);
    }

    $token = $matched->createToken('pin-access')->plainTextToken;

    LoginHistory::create([
        'user_id'    => $matched->id,
        'action'     => 'login',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    return response()->json([
        'token' => $token,
        'user'  => $matched,
    ]);
}}
