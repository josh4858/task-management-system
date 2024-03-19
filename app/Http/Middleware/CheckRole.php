<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => "Unauthorized Access"], 401);
        }

        // Get the current user
        $user = Auth::user();

        $userRoleName = $user->role->name ?? null; // Adjust based on your actual relationship/method

        // Check if the user's role name is in the list of allowed roles
        if ($userRoleName === null || !in_array($userRoleName, $roles)) {
            return response()->json(['message' => "Unauthorized Access"], 401);
        }

        return $next($request);
    }
}
