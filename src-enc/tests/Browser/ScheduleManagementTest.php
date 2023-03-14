<?php

namespace Tests\Browser;

use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\ScheduleManagementPage;
use Tests\DuskTestCase;

class ScheduleManagementTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    private Collection $encounters;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_upcoming_schedules(): void
    {
        $encounter = $this->encounters->random();

        $this->browse(function (Browser $browser) use ($encounter) {
            $browser->loginAs($this->admin)
                ->visit(new ScheduleManagementPage())
                ->assertSee($encounter->uuid);

        });
    }

    public function test_view_cancelled_schedules(): void
    {
        $encounter = $this->encounters->random();

        $this->browse(function (Browser $browser) use ($encounter) {
            $browser->loginAs($this->admin)
                ->visit('/admin/encounters/cancelled')
                ->assertSee($encounter->uuid);

        });
    }

    public function test_view_all_schedules(): void
    {
        $encounter = $this->encounters->random();

        $this->browse(function (Browser $browser) use ($encounter) {
            $browser->loginAs($this->admin)
                ->visit('/admin/encounters/all')
                ->assertSee($encounter->uuid);

        });
    }

    public function test_view_completed_schedules(): void
    {
        $encounter = $this->encounters->random();

        $this->browse(function (Browser $browser) use ($encounter) {
            $browser->loginAs($this->admin)
                ->visit('/admin/encounters/completed')
                ->assertSee($encounter->uuid);

        });
    }


    public function test_attendees_schedule(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new ScheduleManagementPage())
                ->click('#attendees_list')
                ->pause(1000)
                ->assertSee('Attendees of encounter:');

        });
    }

    public function test_detail_schedule(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new ScheduleManagementPage())
                ->click('#view-button')
                ->pause(1000)
                ->assertSee('Encounter:');

        });
    }

    private function prepare_test_data(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create();
        $agent = User::factory()->twoFactorConfirmed()->agent()->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);
        $flows = Flow::factory()->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);
        $this->encounters = Encounter::factory(mt_rand(1, 4))->create([
            'organisations_id' => $this->admin->organisations_id,
            'flows_id' => $flows->id,
            'places_id' => $place->id,
            'agent_id' => $agent->id
        ]);


    }
}
