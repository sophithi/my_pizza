<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has one of the required roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // User doesn't have required role
        return response("Unauthorized - You don't have access to this resource.", 403);
    }
}
