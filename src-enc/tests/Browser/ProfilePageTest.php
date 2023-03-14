<?php

namespace Tests\Browser;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProfilePage;
use Tests\Browser\Pages\UserPassword;
use Tests\DuskTestCase;

class ProfilePageTest extends DuskTestCase
{

    const CURRENT_PASSWORD = "password";
    const PASSWORD = "*!PaSsWoRd";

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(new ProfilePage())
                ->assertSee('Timezone')
                ->click('#edit_profile')
                ->pause(1000)
                ->assertSee('Edit Profile');
        });
    }

    public function test_super_admin_can_update_organisation(): void
    {
        $organisation = Organisation::factory()
            ->create();
        $user = User::factory()
            ->superAdmin()
            ->twoFactorConfirmed()
            ->create(
                [
                    'organisations_id' => $organisation->id
                ]
            );

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(new ProfilePage())
                ->pause(1000)
                ->screenshot('org1')
                ->assertSee('Organisation Name')
                ->click('#edit_organisation')
                ->pause(1000)
                ->assertSee('Edit Organisation Details')
                ->pause(1000)
                ->typeSlowly('#name', Str::random(2))
                ->pause(1000)
                ->click('@edit_org')
                ->pause(1000)
                ->assertSee(trans('validation.name_min', ['min' => 3]));

        });
    }


}
