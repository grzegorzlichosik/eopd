<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;


class EnsureTwoFactorIsEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {

        $redirectToRoute = $redirectToRoute != null ? $redirectToRoute : 'two-factor.set';
        if (
            !$request->user() ||
            !$request->user()->hasEnabledTwoFactorAuthentication()
        ) {
            return $request->expectsJson()
                ? abort(403, 'Your Two Factor Authentication is not enabled.')
                : Redirect::guest(URL::route($redirectToRoute));
        }

        return $next($request);
    }
}
