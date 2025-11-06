<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد (Admin أو Author).
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        // 1. التحقق من صحة البيانات
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed|min:6'
            // ملاحظة: يمكن إضافة حقل 'role' للسماح للمستخدم بتحديد دوره، لكن للأمان، نفضل أن يكون دور المؤلف (author) هو الافتراضي.
        ]);

        // 2. إنشاء المستخدم
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role' => 'author' // دور افتراضي، يمكن تعديله لاحقاً من قبل Admin
        ]);

        // 3. إنشاء Sanctum Token
        $token = $user->createToken('cms-token')->plainTextToken;

        $response = [
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token
        ];

        return response($response, 201);
    }

    /**
     * تسجيل دخول المستخدم والحصول على Token.
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        // 1. التحقق من صحة البيانات
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // 2. التحقق من بيانات الاعتماد (Authentication)
        if (!Auth::attempt($fields)) {
            return response([
                'message' => 'بيانات الاعتماد غير صحيحة.'
            ], 401);
        }

        // 3. حذف الـ Tokens القديمة (لتجنب التراكم)
        $user = Auth::user();
        $user->tokens()->delete();

        // 4. إنشاء Token جديد
        $token = $user->createToken('cms-token')->plainTextToken;

        $response = [
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token
        ];

        return response($response, 200);
    }

    /**
     * تسجيل خروج المستخدم وحذف الـ Token الحالي.
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        // حذف الـ Token الذي تم استخدامه في الطلب الحالي فقط
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'تم تسجيل الخروج بنجاح.'
        ];
    }
}