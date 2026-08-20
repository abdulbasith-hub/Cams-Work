<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CSPMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent iframe embedding
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'none';"); // OR 'self' for same-origin

        return $response;
    }
}
