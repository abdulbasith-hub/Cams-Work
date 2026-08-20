<?php

namespace App\Http\Middleware;

use App\Support\HelpdeskSession;
use Closure;
use Illuminate\Http\Request;

class HelpdeskAccess
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(HelpdeskSession::canAccess(), 403);

        return $next($request);
    }
}
