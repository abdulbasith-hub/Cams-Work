<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
/*    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
} */


public function handle($request, Closure $next)
{
    $response = $next($request);

    return $response->header('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload')
        ->header('X-Content-Type-Options', 'nosniff')
        ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->header('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');
}



}
