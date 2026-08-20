<?php

namespace App\Http\Middleware;

use App\Helpers\JWTService;
use Closure;
use Exception;

class JWTAuth
{
    public function handle($request, Closure $next)
    {
        $auth = $request->header('Authorization');

        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return response()->json([
                'status' => false,
                'message' => 'Token not provided'
            ], 401);
        }

        $token = substr($auth, 7);

        try {
            $decoded = JWTService::validateToken($token);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        $request->client_code = $decoded->client_code;

        return $next($request);
    }
}
