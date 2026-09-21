<?php

namespace App\Http\Middleware;

use App\Models\AccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthVerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = substr($request->header('Authorization'), 7);
        if (!AccessToken::findByToken($bearerToken)) {
            return \response()->json([
                "message" => 'Invalid authorization token'
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
