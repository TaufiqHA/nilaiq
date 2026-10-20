<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Grant lifetime access to a user.
     */
    public function grant(User $user, SubscriptionService $subscriptions): RedirectResponse
    {
        $subscriptions->grantLifetime($user);

        return back()->with('success', 'Akses selamanya berhasil diberikan kepada '.$user->name.'.');
    }

    /**
     * Extend a user subscription by one year.
     */
    public function extend(User $user, SubscriptionService $subscriptions): RedirectResponse
    {
        $subscriptions->activate($user, Plan::annual(), Subscription::SOURCE_ADMIN);

        return back()->with('success', 'Langganan '.$user->name.' berhasil diperpanjang satu tahun.');
    }

    /**
     * Revoke every active subscription of a user.
     */
    public function revoke(Request $request, User $user, SubscriptionService $subscriptions): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat mencabut langganan akun sendiri.');
        }

        $subscriptions->revoke($user);

        return back()->with('success', 'Langganan '.$user->name.' berhasil dicabut.');
    }
}
