<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. مسار إنشاء حساب جديد
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', //confirmed تعني أنه يجب إرسال حقل password_confirmation
            'role' => 'required|string|exists:roles,role_name'
            ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

         $role = Role::where('role_name', $fields['role'])->first();
         $user->roles()->attach($role->id);

        // توليد التوكن للمستخدم الجديد
        $token = $user->createToken('myapp_token')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح',
            'user' => $user->load('roles'),
            'token' => $token
        ], 201);
    }

    // 2. مسار تسجيل الدخول
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // التحقق من الإيميل
        $user = User::where('email', $fields['email'])->first();

        // التحقق من كلمة المرور
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        // توليد التوكن
        $token = $user->createToken('myapp_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
