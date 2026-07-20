<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MapelMiddleware
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

        if ($request->user()->role !== 'mapel') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. Access restricted to Mapel.'], 403);
            }

            return redirect()->route('wali-kelas.dashboard')->with('error', 'Akses ditolak. Halaman ini khusus untuk Guru Mapel.');
        }

        return $next($request);
    }
}
