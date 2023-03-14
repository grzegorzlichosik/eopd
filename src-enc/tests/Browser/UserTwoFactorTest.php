<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\ConfirmTwoFactor;
use Tests\Browser\Pages\UserTwoFactor;
use Tests\DuskTestCase;

class UserTwoFactorTest extends DuskTestCase
{
    public function test_user_can_reset_two_factor_device(): void
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(new UserTwoFactor())
                ->assertPathIs((new UserTwoFactor())->url())
                ->assertSee('Two Factor Authentication')
                ->click('@reset')
                ->pause(1000)
                ->assertSee('Confirm Two Factor Authentication');
        });
    }


    public function test_user_can_see_recovery_codes(): void
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(new UserTwoFactor())
                ->assertPathIs((new UserTwoFactor())->url())
                ->assertSee('Two Factor Authentication')
                ->click('@show')
                ->pause(2000)
                ->assertSee('Confirm Two Factor Authentication');
        });
    }
}
