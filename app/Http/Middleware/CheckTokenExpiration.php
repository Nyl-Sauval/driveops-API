<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // ⚡ Ne pas vérifier ces routes
        if ($request->is('api/login') || $request->is('api/register')) {
            return $next($request);
        }

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at))) {
            return response()->json(['message' => 'Token expired or invalid'], 401);
        }

        return $next($request);
    }
}
