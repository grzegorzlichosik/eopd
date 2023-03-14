<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{

    /**
     * Password age in days
     */
    const PASSWORD_AGE = 180;

    /**
     * Max failed logins
     */
    const FAILED_LOGINS_LIMIT = 5;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string)$request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            /**
             * No user found for provided email
             */
            if (!$user) {
                throw ValidationException::withMessages([
                    Fortify::username() => trans('auth.failed'),
                ]);
            }

            /**
             * User exists so lets check if password matches
             */
            if (Hash::check($request->password, $user->password)) {

                /**
                 * Check failed logins
                 */
                $this->checkFailedLogins($user);

                /**
                 * Check password age
                 */
                $this->checkPasswordAge($user);

                /**
                 * Reset previous failed logins
                 */
                $user->failed_logins = 0;
                $user->save();

                return $user;
            } else {

                /**
                 * Check failed logins
                 */
                $this->checkFailedLogins($user);

                /**
                 * Wrong password
                 */
                $user->increment('failed_logins');

                throw ValidationException::withMessages([
                    Fortify::username() => trans('auth.failed'),
                ]);
            }
        });
    }

    private function checkFailedLogins(User $user): void
    {
        if ($user->failed_logins >= self::FAILED_LOGINS_LIMIT) {
            throw ValidationException::withMessages([
                Fortify::username() => trans('auth.failed_logins_limit', [
                    'link' => '<a class="' . $this->getErrorClass() . '" href="/forgot-password"> here</a>'
                ]),
            ]);
        }
    }

    private function checkPasswordAge(User $user): void
    {
        if (now()->diffInDays($user->password_updated_at, true) >= self::PASSWORD_AGE) {
            throw ValidationException::withMessages([
                Fortify::username() => trans('auth.password_too_old', [
                    'link' => '<a class="' . $this->getErrorClass() . '" href="/forgot-password"> here</a>'
                ]),
            ]);
        }
    }

    private function getErrorClass(): string
    {
        return 'inline-flex items-center px-1 py-1/2 bg-red-700 border
                border-transparent rounded-md font-semibold text-xs text-white tracking-widest
                hover:bg-red-800 active:bg-red-900 focus:outline-none focus:bg-red-900
                focus:ring focus:ring-red-300 disabled:opacity-25 transition';
    }
}
