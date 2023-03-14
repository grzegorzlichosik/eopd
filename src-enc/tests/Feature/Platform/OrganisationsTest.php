<?php

namespace Tests\Feature\Platform;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OrganisationsTest extends TestCase
{
    use WithFaker;

    private User $admin;
    private Collection $organisations;
    private int $count;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        $this->prepare_test_data();
    }

    public function test_can_view_organisations_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('platform.organisations.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_organisations()
    {
        $this->count = Organisation::whereNull('is_platform')->count();
        $this->withoutMiddleware()->actingAs($this->admin)->get(route('platform.organisations.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Platform/Organisations')
                    ->has('organisations.data', min($this->count, 20))
            );
    }

    public function test_can_filter_organisations_ascending()
    {
        $response = $this->withoutMiddleware()->actingAs($this->admin)->get(route('platform.organisations.index', [
            'search' => $this->organisations->random()->name,
            'sortField' => 'name',
            'sortOrder' => 1
        ]));
        $response->assertStatus(200);
    }

    public function test_can_filter_organisations_descending()
    {
        $response = $this->withoutMiddleware()->actingAs($this->admin)->get(route('platform.organisations.index', [
            'search' => $this->organisations->random()->name,
            'sortField' => 'name',
            'sortOrder' => -1
        ]));
        $response->assertStatus(200);
    }

    public function test_can_filter_organisations()
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->admin)
            ->get(route('platform.organisations.index', [
            'search' => $this->organisations->random()->name,
        ]));
        $response->assertStatus(200);
    }

    public function test_can_see_master_calendar()
    {
        $organisation = Organisation::factory()->create();
        $organisation->master_calendar_password = Str::random();
        $organisation->master_calendar_email = $this->faker->email;
        $organisation->save();

        $response = $this->withoutMiddleware()
            ->actingAs($this->admin)
            ->get(route('platform.organisations.master_calendar.show', [
            'uuid' => $organisation->uuid,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'password' => $organisation->master_calendar_password,
            'email'    => $organisation->master_calendar_email,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->admin)
            ->get(route('platform.organisations.master_calendar.show', [
                'uuid' => Str::uuid()->toString(),
            ]));

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'password' => $organisation->master_calendar_password,
            'email'    => $organisation->master_calendar_email,
        ]);
    }

    private function prepare_test_data(): void
    {
        $platformOrganisation = Organisation::where('is_platform', 1)->first();

        $this->admin = User::factory()->twoFactorConfirmed()
            ->create(['organisations_id' => $platformOrganisation->id]);

        $this->organisations = Organisation::factory(20)->create();
        foreach ($this->organisations as $organisation) {
            $admins = User::factory(5)->admin()->unverified()->create(['organisations_id' => $organisation->id]);
            $organisation->created_by = $admins->first()->id;
            $organisation->save();
        }
    }
}
