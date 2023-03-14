<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UsersLatestLogin;
use Illuminate\Auth\Events\Login;

class UserLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        /**
         * @var User $user
         */
        $user = $event->user;
        $user->last_login_at = now();
        $user->two_factor_reset_request_at = null;
        $user->save();

        $userLogin = new UsersLatestLogin();
        $userLogin->users_id = $user->id;
        $userLogin->ip = request()->ip();
        $userLogin->save();
    }
}
