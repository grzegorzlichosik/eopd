<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\GroupsPage;
use Tests\Browser\Pages\DashboardPage;
use Tests\DuskTestCase;

class LinkMyCalendarTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    public function test_link_my_calendar_button_visible(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create();
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new DashboardPage())
                ->assertSee('Link My Calendar');
        });
    }

    public function test_link_my_calendar_button_not_visible(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create([
            'nylas_access_token' => Str::random()
        ]);
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new DashboardPage())
                ->assertNotPresent('Link My Calendar');
        });
    }

}
