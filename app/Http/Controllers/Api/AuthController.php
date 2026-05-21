<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password) || !$user->is_active) {
            throw ValidationException::withMessages(['email' => 'Kredensial salah atau akun nonaktif.']);
        }

        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

        $token = $user->createToken($request->device_name, ['employee:read', 'attendance:write', 'leave:write', 'reimbursement:write']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $employee = \App\Models\Employee::where('user_id', $user->id)->first();

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'tenant_id']),
            'employee' => $employee ? $employee->only([
                'id', 'employee_no', 'full_name', 'employment_status',
                'employment_type', 'join_date', 'photo_path',
                'company_id', 'branch_id', 'department_id', 'position_id',
            ]) : null,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
