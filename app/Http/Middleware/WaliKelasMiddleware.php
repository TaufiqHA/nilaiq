<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaliKelasMiddleware
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

        if ($request->user()->role !== 'wali_kelas') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. Access restricted to Wali Kelas.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini khusus untuk Wali Kelas.');
        }

        return $next($request);
    }
}
