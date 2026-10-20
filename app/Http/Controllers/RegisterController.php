<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new teacher and start the free trial.
     */
    public function store(Request $request, SubscriptionService $subscriptions): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:mapel,wali_kelas'],
        ]);

        /** @var User $user */
        $user = User::create($validated);

        $subscriptions->startTrial($user, Plan::annual());

        Auth::login($user);
        $request->session()->regenerate();

        $targetRoute = $user->role === 'wali_kelas'
            ? route('wali-kelas.dashboard')
            : route('dashboard');

        return redirect($targetRoute)->with(
            'success',
            'Selamat datang di NilaiQ! Trial gratis Anda aktif selama '.config('subscription.trial_days', 3).' hari.'
        );
    }
}
