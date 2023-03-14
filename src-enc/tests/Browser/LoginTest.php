<?php

namespace Tests\Browser;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ForgotPassword;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\SetTwoFactorAuthentication;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    private const PASSWORD = "password";
    private const INCORRECT_PASSWORD = "incorrect_password";

    public function test_success_login(): void
    {
        $user = User::factory()->twoFactorNotConfirmed()->create([
            'password' => Hash::make(self::PASSWORD),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Login())
                ->typeSlowly('@email', $user->email)
                ->typeSlowly('@password', self::PASSWORD)
                ->click('@login')
                ->pause(2000)
                ->assertPathIs('/user/set-two-factor-authentication');
        });
    }

    public function test_failed_login_incorrect_password(): void
    {
        $user = User::factory()->requiresPasswordUpdate()->create([
            'password' => Hash::make(self::PASSWORD)
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Login())
                ->typeSlowly('@email', $user->email)
                ->typeSlowly('@password', self::INCORRECT_PASSWORD)
                ->click('@login')
                ->pause(1000)
                ->assertRouteIs('login')
                ->assertSee(trans('auth.failed'));
        });
    }

    public function test_failed_login_as_password_is_too_old(): void
    {
        $user = User::factory()->requiresPasswordUpdate()->create([
            'password' => Hash::make(self::PASSWORD)
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Login())
                ->typeSlowly('@email', $user->email)
                ->typeSlowly('@password', self::PASSWORD)
                ->click('@login')
                ->pause(1000)
                ->assertRouteIs('login')
                ->assertSee(trim(trans('auth.password_too_old', [
                    'link' => ''
                ])));
        });
    }

    public function test_failed_login_as_max_failed_logins_reached(): void
    {
        $user = User::factory()->maxFailedLogins()->create([
            'password' => Hash::make(self::PASSWORD)
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Login())
                ->typeSlowly('@email', $user->email)
                ->typeSlowly('@password', self::PASSWORD)
                ->click('@login')
                ->pause(1000)
                ->assertRouteIs('login')
                ->assertSee(trim(trans('auth.failed_logins_limit', [
                    'link' => ''
                ])));
        });
    }

    public function test_can_click_forgot_password()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->click("@forgot-password")
                ->pause(1000)
                ->assertPathIs((new ForgotPassword())->url());
        });
    }
}
