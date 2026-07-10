<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
  
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // Require password confirmation on signup for safety (frontend should send password_confirmation)
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        try {
            // Prepare user data. Only set `role` if the database has the column to avoid migration issues.
            $data = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ];

            if (Schema::hasColumn('users', 'role')) {
                $data['role'] = 'client';
            }

            $user = User::create($data);

            // Log the user in using session auth (works with Sanctum SPA cookie flow).
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error while creating user'], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $credentials = $request->only('email', 'password');

        // Attempt to authenticate using session guard. Frontend must call /sanctum/csrf-cookie first.
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Prevent session fixation
        $request->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
        ], 200);
    }

    /**
     * Logout the current user (invalidate session/cookie)
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out'], 200);
    }

    /**
     * Request a password reset token. Token is stored hashed in `password_reset_tokens`.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $email = $request->input('email');

        // Always return success to avoid revealing whether an email exists.
        $user = User::where('email', $email)->first();
        if ($user) {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            // TODO: send reset email with token (not implemented here). For local testing, admin may fetch token from DB.
        }

        return response()->json(['message' => 'If your email exists, a reset link will be sent.'], 200);
    }

    /**
     * Reset password using token sent to user's email.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $record = DB::table('password_reset_tokens')->where('email', $request->input('email'))->first();
        if (!$record) {
            return response()->json(['message' => 'Invalid or expired token'], 400);
        }

        // Check token expiry
        $expires = config('auth.passwords.users.expire', 60);
        if (strtotime($record->created_at) < now()->subMinutes($expires)->timestamp) {
            return response()->json(['message' => 'Token expired'], 400);
        }

        if (!Hash::check($request->input('token'), $record->token)) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Remove used token
        DB::table('password_reset_tokens')->where('email', $request->input('email'))->delete();

        // Log the user in after password reset
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['message' => 'Password reset successful', 'user' => $user], 200);
    }

    /**
     * Return authenticated user or 401
     */
    public function user(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return response()->json(['user' => $user], 200);
    }
}
