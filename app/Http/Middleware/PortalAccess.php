<?php

namespace App\Http\Middleware;

use App\Models\PortalModel;
use Closure;
use Illuminate\Http\Request;

class PortalAccess
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(PortalModel::canAccessPortal(), 403);

        return $next($request);
    }
}
