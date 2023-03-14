<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class IsSuperAdminOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $authUser = auth()->user();

        if ($authUser->is_admin || $authUser->is_super_admin) {
            return $next($request);
        }
        return Redirect::guest(URL::route('dashboard'));
    }
}
