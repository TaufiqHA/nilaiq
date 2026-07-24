<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        if ($request->user()->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. Access restricted to Admin.'], 403);
            }

            $target = $request->user()->role === 'wali_kelas'
                ? route('wali-kelas.dashboard')
                : route('dashboard');

            return redirect($target)->with('error', 'Akses ditolak. Halaman ini khusus untuk Admin.');
        }

        return $next($request);
    }
}
