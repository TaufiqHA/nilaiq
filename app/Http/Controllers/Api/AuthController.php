<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate a mobile user and issue a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if ($user->role !== 'mapel') {
            return response()->json([
                'message' => 'Akun ini tidak memiliki akses ke aplikasi mobile. Halaman ini khusus untuk Guru Mapel.',
            ], 403);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'mobile')->plainTextToken;
        $subscription = $user->currentSubscription();

        return response()->json([
            'token' => $token,
            'user' => $user,
            'has_access' => $user->hasAppAccess(),
            'subscription' => [
                'status' => $subscription?->status ?? 'inactive',
                'status_label' => $user->subscriptionStatusLabel(),
                'is_trial' => $user->isOnTrial(),
                'days_left' => $user->subscriptionDaysLeft(),
                'ends_at' => $subscription?->ends_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Return the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Lightweight endpoint used by the mobile app to verify real connectivity.
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'time' => now()->toIso8601String(),
        ]);
    }
}
