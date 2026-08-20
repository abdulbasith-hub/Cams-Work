<?php

namespace App\Http\Middleware;

use App\Services\HelpdeskV2Session;
use Closure;
use Illuminate\Http\Request;

class HelpdeskV2Access
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(HelpdeskV2Session::canAccess(), 403);

        return $next($request);
    }
}
