<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('branch')->latest();

        if ($request->user()->role === 'hq') {
            $query->where('role', 'driver');
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'vehicle_plate' => 'nullable|string|max:20',
            'email'         => 'sometimes|nullable|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'role'          => 'required|in:admin,hq,driver,branch',
            'branch_id'     => 'nullable|exists:branches,id',
            'pin'           => 'nullable|digits:4',
            'employee_code' => 'nullable|string|max:50|unique:users,employee_code',
        ]);

        $email = !empty($request->email)
            ? $request->email
            : strtolower(preg_replace('/\s+/', '', $request->name)) . '_' . time() . '@bh-logistic.com';

        $user = User::create([
            'name'          => $request->name,
            'phone'         => $request->phone ?: null,
            'vehicle_plate' => $request->vehicle_plate ?: null,
            'email'         => $email,
            'password'      => $request->password,
            'role'          => $request->role,
            'branch_id'     => $request->branch_id,
            'employee_code' => $request->employee_code ?: null,
            'pin'           => $request->filled('pin') ? Hash::make($request->pin) : null,
            'is_active'     => true,
        ]);

        return response()->json($user->load('branch'), 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'phone'         => 'sometimes|nullable|string|max:20',
            'vehicle_plate' => 'sometimes|nullable|string|max:20',
            'email'         => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password'      => 'sometimes|string|min:8',
            'role'          => 'sometimes|in:admin,hq,driver,branch',
            'branch_id'     => 'sometimes|nullable|exists:branches,id',
            'is_active'     => 'sometimes|boolean',
            'pin'           => 'sometimes|nullable|digits:4',
            'employee_code' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('users', 'employee_code')->ignore($user->id)],
        ]);

        $data = $request->only(['name', 'phone', 'vehicle_plate', 'email', 'password', 'role', 'branch_id', 'is_active', 'employee_code']);
        if ($request->filled('pin')) {
            $data['pin'] = Hash::make($request->pin);
        }
        $user->update($data);

        return response()->json($user->load('branch'));
    }

    public function resetPin(Request $request, User $user)
    {
        $request->validate(['pin' => 'required|digits:4']);
        $user->update(['pin' => Hash::make($request->pin)]);
        return response()->json(['message' => 'รีเซ็ต PIN สำเร็จ']);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
