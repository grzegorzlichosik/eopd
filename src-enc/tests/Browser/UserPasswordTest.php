<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UserPassword;
use Tests\DuskTestCase;

class UserPasswordTest extends DuskTestCase
{

    const CURRENT_PASSWORD = "password";
    const PASSWORD = "*!PaSsWoRd";

    public function test_user_can_reset_password(): void
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(new UserPassword())
                ->assertPathIs((new UserPassword())->url())
                ->assertSee('Update Password')
                ->typeSlowly('@currentPassword', self::CURRENT_PASSWORD)
                ->typeSlowly('@newPassword', self::PASSWORD)
                ->typeSlowly('@confirmPassword', self::PASSWORD)
                ->click('#save-button')
                ->pause(1000)
                ->assertSee('Confirm Two Factor Authentication');
        });
    }
}
