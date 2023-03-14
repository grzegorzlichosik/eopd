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

class PoolUserTest extends TestCase
{
    use WithFaker;

    public function test_can_store_pool_users()
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.pools.users.store', [
            'uuid'           => $pool->uuid,
            'selected_users' => [['name' => $admin->name, 'uuid' => $admin->uuid]]
        ]));
        $this->assertDatabaseHas('pools_users_map', [
            'pools_id' => $pool->id,
            'users_id' => $admin->id
        ]);

        $response->assertRedirect(route('admin.pools.show', [$pool->uuid]));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.pool_users', ['pool' => $pool->name]));
        $response->assertStatus(303);

    }

    public function test_cannot_store_pool_users(): void
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);

        for ($i = 0; $i < 2; $i++) {
            $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.pools.users.store', [
                'uuid'           => $pool->uuid,
                'selected_users' => [['name' => $admin->name, 'uuid' => $admin->uuid]]
            ]));
        }

        $response->assertRedirect(route('admin.pools.show', [$pool->uuid]));
        $response->assertSessionHas('toaster');
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(303);

    }

    public function test_can_destroy_pool_users()
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->delete(route('admin.pools.users.delete', ['uuid' => $pool->uuid, 'user_uuid' => $admin->uuid])
            );

        $response->assertRedirect(route('admin.pools.show', [$pool->uuid]));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.pool_user_deleted', ['pool' => $pool->name, 'user' => $admin->name]));
        $response->assertStatus(303);

    }

    public function test_cannot_destroy_pool_users()
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->delete(route('admin.pools.users.delete', ['uuid' => $pool->uuid, 'user_uuid' => Str::random()])
            );

        $response->assertStatus(303);

    }

    public function test_can_search_users()
    {
        $user = User::factory()->superAdmin()->create();
        $orgUser = User::factory()->create([
            'name'             => $this->faker->name,
            'organisations_id' => $user->organisations_id
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $user->organisations_id
        ]);
        $pool->users()->attach($orgUser->id);
        $response = $this->withoutMiddleware()->actingAs($user)->getJson(route('admin.pools.users.search', [
            'uuid'     => $pool->uuid,
            'search'   => $orgUser->name,
            'selected' => [$orgUser->uuid]
        ]));

        $response->assertStatus(200);

    }

}
