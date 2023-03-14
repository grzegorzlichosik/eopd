<?php

namespace Tests\Feature\Admin;

use App\Models\Pool;
use App\Models\Organisation;
use App\Models\User;
use App\Notifications\Reset2FAUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Notifications\NewUserInvite;

class PoolTest extends TestCase
{
    use WithFaker;

    public function test_super_admin_can_view_pools(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->actingAs($user)->get(route('admin.pools'));
        $response->assertStatus(302);

    }

    public function test_super_admin_can_view_groups_sorting_asc(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->actingAs($user)->get(route('admin.pools', [
            'sortField' => 'name',
            'sortOrder' => '-1'
        ]));
        $response->assertStatus(302);

    }

    public function test_super_admin_can_view_groups_sorting_desc(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->actingAs($user)->get(route('admin.pools', [
            'sortField' => 'name',
            'sortOrder' => '1'
        ]));
        $response->assertStatus(302);

    }

    public function test_can_view_pools()
    {
        $user = User::factory()->superAdmin()->create();
        $this->withoutMiddleware()->actingAs($user)->get(route('admin.pools'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Pools')
            );
    }


    public function test_can_show_pool()
    {
        $user = User::factory()->superAdmin()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $user->organisations_id
        ]);
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.pools.show', ['uuid' => $pool->uuid]));
        $response->assertStatus(200);
    }

    public function test_can_show_group_users_descending()
    {
        $user = User::factory()->superAdmin()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $user->organisations_id
        ]);
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.pools.show', [
            'uuid'      => $pool->uuid,
            'sortField' => 'email',
            'sortOrder' => -1
        ]));
        $response->assertStatus(200);
    }

    public function test_can_show_group_users_ascending()
    {
        $user = User::factory()->superAdmin()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $user->organisations_id
        ]);
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.pools.show', [
            'uuid'      => $pool->uuid,
            'sortField' => 'email',
            'sortOrder' => 1
        ]));
        $response->assertStatus(200);
    }

    public function test_cannot_show_pool()
    {
        $user = User::factory()->superAdmin()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $user->organisations_id
        ]);
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.pools.show', ['uuid' => Str::random()]));
        $response->assertStatus(404);
    }


    public function test_can_create_pools()
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.pools.create'), [
                'name' => $this->faker->name,
            ]);

        $this->assertDatabaseHas(
            'pools',
            [
                'name' => request('name'),
            ]
        );


        $response->assertRedirect(route('admin.pools'));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.pool_added', ['pool' => request('name')]));
        $response->assertStatus(302);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.pools.create'), [
                'name' => Str::random(2),
            ]);

        $response->assertSessionHasErrors(['name']);
        $response->assertStatus(302);
    }

    public function test_can_update_pool()
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.pools.update', ['uuid' => $pool->uuid]), [
                'name' => $this->faker->name,
            ]);

        $this->assertDatabaseHas(
            'pools',
            [
                'name' => request('name'),
            ]
        );


        $response->assertRedirect(route('admin.pools.show', ['uuid' => $pool->uuid]));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.pool_edited', ['pool' => request('name')]));
        $response->assertStatus(303);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.pools.create'), [
                'name' => Str::random(2),
            ]);

        $response->assertSessionHasErrors(['name']);
        $response->assertStatus(302);
    }

    public function test_cannot_update_pool()
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.pools.update', ['uuid' => Str::random()]), [
                'name' => $this->faker->name,
            ]);
        $response->assertStatus(303);
        $response->assertLocation(route('admin.pools'));
        $response->assertSessionHas('toaster');
        $this->assertEquals('error', session('toaster')['type']);
    }

}
