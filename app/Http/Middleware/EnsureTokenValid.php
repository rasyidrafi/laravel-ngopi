<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureTokenValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json([
            "status" => "error",
            "message" => "Unauthorized"
        ], 401);

        $user = User::where("token", $token)->where("is_login", true)->first();
        if (!$user) return response()->json([
            "status" => "error",
            "message" => "Unauthorized"
        ], 401);

        $request->user = $user;

        return $next($request);
    }
}
