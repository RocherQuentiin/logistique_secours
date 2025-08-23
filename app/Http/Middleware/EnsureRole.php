<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: ensure.role:admin (allows admin,dev) | ensure.role:dev (allows dev)
     */
    public function handle(Request $request, Closure $next, string $required): Response
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $rank = ['user' => 1, 'admin' => 2, 'dev' => 3];
        $have = $rank[$user->role] ?? 0;
        $need = $rank[$required] ?? 99;

        if ($have < $need) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
