<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserInvite;
use App\Validators\EmailValidator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AccountActivationController extends Controller
{
    public function __construct(private EmailValidator $emailValidator)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Auth/AccountActivation');
    }

    public function store(Request $request): \Inertia\Response
    {
        $request = $request->all();
        $this->emailValidator->validate($request);
        $user = User::where('email', $request['email'])->first();
        if ($user) {
            $token = Password::broker(config('fortify.passwords'))->createToken($user);
            $user->notify(new NewUserInvite($token));
            response()->json(['success' => 'success']);

        }
        return Inertia::render('Auth/AccountActivation', [
            'status' => (trans('auth.account_activation')),
        ]);


    }

}
