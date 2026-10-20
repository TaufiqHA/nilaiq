<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class SubscriptionController extends Controller
{
    /**
     * Return the current subscription entitlement for the mobile app.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->currentSubscription();
        $plan = Plan::annual();

        return response()->json([
            'has_access' => $user->hasAppAccess(),
            'is_trial' => $user->isOnTrial(),
            'status' => $subscription?->status ?? 'inactive',
            'status_label' => $user->subscriptionStatusLabel(),
            'days_left' => $user->subscriptionDaysLeft(),
            'ends_at' => $subscription?->ends_at?->toIso8601String(),
            'plan' => $plan === null ? null : [
                'code' => $plan->code,
                'name' => $plan->name,
                'price' => $plan->price,
                'original_price' => $plan->original_price,
                'duration_days' => $plan->duration_days,
            ],
        ]);
    }

    /**
     * Create a Snap transaction for the mobile app.
     */
    public function checkout(Request $request, CheckoutService $checkout): JsonResponse
    {
        try {
            $order = $checkout->createOrder($request->user());
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'order_id' => $order->order_id,
            'gross_amount' => $order->gross_amount,
            'token' => $order->snap_token,
            'redirect_url' => $order->snap_redirect_url,
            'expires_at' => $order->expires_at?->toIso8601String(),
        ]);
    }
}
