<?php

namespace Tests\Unit\Models;

use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Pool;
use App\Models\Organisation;
use App\Models\User;
use App\Models\UsersLatestLogin;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_users(): void
    {
        User::factory(10)->create()->each(function ($user) {
            $this->assertDatabaseHas(
                'users',
                [
                    'uuid' => $user->uuid,
                    'name' => $user->name
                ]
            );
        });
    }

    public function test_users_organisation_relation(): void
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $this->assertInstanceOf(Organisation::class, $user->organisation);
        $this->assertEquals($user->organisations_id, $user->organisation->id);
    }

    public function test_users_latest_login_relation(): void
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        UsersLatestLogin::factory(10)->create([
            'users_id' => $user->id,
        ])->each(function ($userLatestLogin) use ($user) {
            $this->assertDatabaseHas(
                'users_latest_logins',
                [
                    'users_id' => $user->id
                ]
            );
        });

        $this->assertInstanceOf(UsersLatestLogin::class, $user->latestLogin->first());
        $this->assertEquals($user->id, $user->latestLogin->first()->users_id);
    }

    public function test_user_pools_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $pools = Pool::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $user = User::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $user->pools()->sync(
            $pools->map(fn($item) => $item->id)->toArray()
        );

        $this->assertEquals(10, $user->pools->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->pools);
        $this->assertInstanceOf(Pool::class, $user->pools->first());
    }

    public function test_user_flows_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $flows = Flow::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $pools = Pool::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $user = User::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $user->flows()->syncWithPivotValues(
            $flows->map(fn($item) => $item->id)->toArray(),
            [
                'pools_id' => $pools->random()->id,
            ]
        );

        $this->assertEquals(10, $user->flows->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->flows);
        $this->assertInstanceOf(Flow::class, $user->flows->first());
    }

    public function test_user_encounters_relation(): void
    {
        $user = User::factory()->create();

        Encounter::factory(10)->create(
            [
                'agent_id' => $user->id,
            ]
        )
            ->each(function ($encounter)  use ($user){
                $this->assertDatabaseHas(
                    'encounters',
                    [
                        'agent_id' => $user->id,
                    ]
                );
            });

        $this->assertEquals(10, $user->encounters->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->encounters);
        $this->assertInstanceOf(Encounter::class, $user->encounters->first());
    }

    public function test_is_admin_user(): void
    {
        $user = User::factory()->admin()->create();
        $this->assertTrue($user->is_admin);
        $this->assertNull($user->is_super_admin);
    }

    public function test_is_super_admin_user(): void
    {
        $user = User::factory()->superAdmin()->create();
        $this->assertNull($user->is_admin);
        $this->assertTrue($user->is_super_admin);
    }

    public function test_is_agent_user(): void
    {
        $user = User::factory()->agent()->create();
        $this->assertTrue($user->is_agent);
    }

    public function test_is_developer_user(): void
    {
        $user = User::factory()->developer()->create();
        $this->assertTrue($user->is_developer);
    }

    public function test_set_two_factor_reset_request(): void
    {
        $user = User::factory()->create();
        $user->setTwoFactorResetRequest();
        $this->assertNotNull($user->two_factor_reset_request_at);
    }

    public function test_reset_two_factor_reset_request(): void
    {
        $user = User::factory()->create();
        $user->resetTwoFactorResetRequest();
        $this->assertNull($user->two_factor_reset_request_at);
    }

}
