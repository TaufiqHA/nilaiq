<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->hasAppAccess()) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Masa trial atau langganan Anda sudah berakhir. Silakan perpanjang langganan untuk melanjutkan.',
                'code' => 'subscription_required',
                'has_access' => false,
            ], 402);
        }

        return redirect()
            ->route('billing.index')
            ->with('error', 'Masa trial atau langganan Anda sudah berakhir. Silakan berlangganan untuk melanjutkan.');
    }
}
