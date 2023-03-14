<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Http\Controllers\Inertia\Concerns\ConfirmsTwoFactorAuthentication;
use Laravel\Jetstream\Jetstream;
use Inertia\Response;
use App\Validators\ProfileValidator;


class UserController extends Controller
{
    use ConfirmsTwoFactorAuthentication;

    public function __construct(
        private readonly ProfileValidator $profileValidator,
    )
    {
    }

    public function __two_factor(Request $request): Response
    {
        return Jetstream::inertia()->render($request, 'User/TwoFactor');
    }

    public function __password(Request $request): Response
    {
        return Jetstream::inertia()->render($request, 'User/Password');
    }

    public function timezones(): array
    {
        return \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    }

    public function __update_profile(Request $request): RedirectResponse
    {
        $this->profileValidator->validate($request->all());

        User::where('id', auth()->user()->id)
            ->update(
                [
                    'name'     => $request['name'],
                    'timezone' => $request['timezone']
                ]
            );

        return redirect()->back()
            ->with('toaster', [
                    'message' => trans(
                        'modals.profile_updated'
                    )
                ]
            );
    }

}
