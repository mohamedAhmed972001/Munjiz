<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile; // ⬅️ تم استدعاء الموديل
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // ⬅️ تم استدعاء الـ DB Facade
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // تسجيل مستخدم جديد
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:client,freelancer',
        ]);

        $user = DB::transaction(function () use ($request) {
            
            // 1. إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. تعيين الدور
            $user->assignRole($request->role);

            // 3. ⭐️ إنشاء بروفايل فارغ فوراً (ضمن Transaction)
            $user->profile()->create([]);

            return $user;
        });

        // 4. إنشاء التوكن (بعد نجاح الترانزاكشن)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Welcome to Munjiz! Registration successful.',
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
            'token' => $token,
        ], 201);
    }

    // تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
            'token' => $token,
        ]);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}