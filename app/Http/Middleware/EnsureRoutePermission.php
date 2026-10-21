<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $route = $request->route();

        if (! $route) {
            return $next($request);
        }

        $name = $route->getName();

        if (! $name) {
            return $next($request);
        }

        if ($user->can($name)) {
            return $next($request);
        }

        abort(403);
    }
}

