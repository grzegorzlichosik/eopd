<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\GroupsPage;
use Tests\Browser\Pages\DashboardPage;
use Tests\DuskTestCase;

class LinkRoomCalendarTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    private Organisation $organisation;

    public function test_link_room_calendar_button_visible(): void
    {
        $this->organisation = Organisation::factory()->create([
            'nylas_access_token' => ''
        ]);
        $this->admin = User::factory()->twoFactorConfirmed()->agent()->create([
            'organisations_id' => $this->organisation->id
        ]);

        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new DashboardPage())
                ->screenshot('room')
                ->assertSee('Link Room Calendar');
        });
    }

    public function test_link_room_calendar_button_not_visible(): void
    {
        $this->organisation = Organisation::factory()->create([
            'nylas_access_token' => Str::random()
        ]);
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create([
            'organisations_id' => $this->organisation->id
        ]);

        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new DashboardPage())
                ->assertNotPresent('Link Room Calendar');
        });
    }

}
