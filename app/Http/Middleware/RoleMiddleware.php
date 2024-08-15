<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || $request->user()->role->name !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
