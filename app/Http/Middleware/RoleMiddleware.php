<?php

namespace App\Http\Middleware;

use App\Enums\UsersRolesEnums;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
        
            Log::info("No authenticated user.");
        
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $user = Auth::user();

        // Validate if the role exists in the UsersRolesEnums
        if (!in_array($role, [UsersRolesEnums::user, UsersRolesEnums::admin],UsersRolesEnums::viewer)) {
           
            Log::info("Invalid role specified: " . $role);
           
            return response()->json(['message' => 'Invalid role.'], 400);
        }

        // Check if the user has the specified role
        if ($user->role !== $role) {
           
            Log::info("Unauthorized role: " . $user->role);
           
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        Log::info("Authorized role: " . $user->role);
        
        return $next($request);
    }
}

