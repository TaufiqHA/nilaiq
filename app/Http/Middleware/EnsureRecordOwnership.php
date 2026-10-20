<?php

namespace App\Http\Middleware;

use App\Services\OwnershipService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRecordOwnership
{
    public function __construct(private OwnershipService $ownership) {}

    /**
     * Reject any route-bound record that belongs to another user.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            foreach ($request->route()?->parameters() ?? [] as $parameter) {
                if ($parameter instanceof Model && ! $this->ownership->isOwnedBy($parameter, $user)) {
                    abort(404);
                }
            }
        }

        return $next($request);
    }
}
