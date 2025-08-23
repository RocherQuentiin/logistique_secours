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
            // For API requests, return JSON 401; for web, redirect to login
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->guest(route('login'));
        }

        $rank = ['user' => 1, 'admin' => 2, 'dev' => 3];
        $have = $rank[$user->role] ?? 0;
        $need = $rank[$required] ?? 99;

        if ($have < $need) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return redirect('/')->withErrors('Accès refusé.');
        }

        return $next($request);
    }
}
