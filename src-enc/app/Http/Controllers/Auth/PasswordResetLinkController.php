<?php

namespace App\Http\Controllers\Auth;

use App\Validators\EmailValidator;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\RequestPasswordResetLinkViewResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController as FortifyPasswordResetLinkController;

class PasswordResetLinkController extends FortifyPasswordResetLinkController
{

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function __construct(private EmailValidator $emailValidator)
    {
    }

    public function store(Request $request): Responsable
    {
        $request = $request->all();
        $this->emailValidator->validate($request);
        $this->broker()->sendResetLink(
            ['email'=>$request['email']]
        );

        return app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => PasswordBroker::RESET_LINK_SENT]);
    }
}
