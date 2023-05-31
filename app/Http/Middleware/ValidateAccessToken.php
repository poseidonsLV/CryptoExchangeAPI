<?php

namespace App\Http\Middleware;

use App\Http\Controllers\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasCookie('access_token')) {
            $user = new User();

            $token = $request->cookie('access_token');

            if (!$user->isTokenValid($token)) {
                return new JsonResponse(['ErrorCode' => -77, 'ErrorMessage' => 'Token Expired!'], 401);
            }
        } else {
            return new JsonResponse(['ErrorCode' => -77, 'ErrorMessage' => 'Token Expired!'], 401);
        }
        return $next($request);
    }
}
