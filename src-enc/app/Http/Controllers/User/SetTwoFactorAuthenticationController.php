<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;

class SetTwoFactorAuthenticationController extends Controller
{
    public function show(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasEnabledTwoFactorAuthentication()
            ? redirect()->intended()
            : Jetstream::inertia()->render($request, 'User/SetTwoFactorAuthentication');
    }
}
