<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class IsPlatformOrganisation
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->organisation?->is_platform === 1) {
            return $next($request);
        }
        return Redirect::guest(URL::route('dashboard'));
    }
}
