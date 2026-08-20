<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;



class JWTService
{
    // public static function generateToken($client_code)
    // {
    //     $payload = [
    //         'client_code' => $client_code,
    //         'iat' => time(),
    //         'exp' => time() + (env('JWT_EXPIRE_MINUTES') * 60),
    //     ];

    //     return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    // }

    public static function validateToken($token)
    {
        return JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
    }

    public static function generateToken($clientCode, $expireMinutes = 30)
    {
        $payload = [
            'client_code' => $clientCode,
            'iat' => time(),
            'exp' => time() + ($expireMinutes * 60)
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
}
