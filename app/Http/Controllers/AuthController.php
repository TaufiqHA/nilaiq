<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember', true))) {
            $request->session()->regenerate();

            $user = $request->user();

            if ($request->wantsJson()) {
                return response()->json($user);
            }

            $targetRoute = $user->role === 'wali_kelas'
                ? route('wali-kelas.dashboard')
                : route('dashboard');

            return redirect()->intended($targetRoute);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully.']);
        }

        return redirect(route('login'));
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json($user);
    }
}
