<?php

namespace Tests\Browser;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\FlowPage;
use Tests\DuskTestCase;

class FlowsTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;
    private Flow $flows;
    private Collection $flow;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_flows(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->assertSee($this->flow->random()->name);

        });
    }

    public function test_create_flow(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->click('#add_flow')
                ->pause(1000)
                ->screenshot('addflows')
                ->assertSee('Create Flow')
                ->typeSlowly('#objective', Str::random(2))
                ->pause(1000)
                ->click('@create_flow')
                ->pause(1000)
                ->assertSee(trans('validation.objective_min', ['min' => 3]));

        });
    }

    public function test_view_flow_detail(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->click('#view_flow')
                ->pause(1000)
                ->screenshot('flows')
                ->assertSee('Flow Name:');

        });
    }

    public function test_edit_flow_detail(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->click('#view_flow')
                ->pause(1000)
                ->screenshot('flows')
                ->assertSee('EDIT')
                ->click('#edit_new_flow')
                ->pause(1000)
                ->assertSee('Edit Flow');

        });
    }

    public function test_add_agents_flow(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->click('#view_flow')
                ->pause(1000)
                ->screenshot('flows')
                ->assertSee('ADD AGENT')
                ->click('#add_agent_flow')
                ->pause(1000)
                ->assertSee('Add Agents');

        });
    }

    public function test_view_resources_flow(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->click('#view_flow')
                ->pause(1000)
                ->screenshot('flows')
                ->assertSee('Resources');

        });
    }

    public function test_view_encounters_flow(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new FlowPage())
                ->click('#view_flow')
                ->pause(1000)
                ->screenshot('flows')
                ->assertSee('Encounters');

        });
    }

    private function prepare_test_data(): void
    {
        $organisation = Organisation::factory()->create();
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create([
            'organisations_id' => $organisation->id,
        ]);
        $agent = User::factory()->twoFactorConfirmed()->agent()->create([
            'organisations_id' => $organisation->id,
        ]);
        $this->flows = Flow::factory()->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);
        $this->flow = Flow::factory(4)->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);

        $channel =  Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $this->flows->id,
            'organisations_id' => $organisation->id,
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $pool->users()->attach($agent->id);

        $this->flows->users()->attach($agent->id, [
            'pools_id' => $pool->id
        ]);
        $this->flows->channelUsers()->attach($agent->id, [
            'channels_id' => $channel->id
        ]);
        $location = Location::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $places = Place::factory()->create([
            'organisations_id' => $organisation->id,
            'locations_id' => $location->id,
        ]);
        $encounter = Encounter::factory()->create([
            'agent_id'         => $agent->id,
            'flows_id'         => $channel->flows_id,
            'organisations_id' => $organisation->id,
            'places_id'        => $places->id,
        ]);
        $channel->places()->attach($places->id);
    }
}
