<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the session has 'user_id'
        if (!session()->has('user')) {
            // If session doesn't have user_id, redirect to login page
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        // Continue to the requested route if session exists
        return $next($request);
    }
}

