<?php

namespace Tests\Unit\Models;

use App\Models\Pool;
use App\Models\Organisation;
use App\Models\User;
use Tests\TestCase;

class PoolTest extends TestCase
{
    public function test_organisation(): void
    {
        Pool::factory(10)->create()->each(function ($pool) {
            $this->assertDatabaseHas(
                'pools',
                [
                    'uuid' => $pool->uuid,
                    'name' => $pool->name
                ]
            );
        });

        $pool = Pool::factory()->create();
        $this->assertModelExists($pool);

        $pool = Pool::factory()->create();
        $pool->delete();
        $this->assertModelMissing($pool);

    }

    public function test_pool_organisation_relation(): void
    {
        $pool = Pool::factory()->create();
        $this->assertModelExists($pool);
        $this->assertInstanceOf(Organisation::class, $pool->organisation);
        $this->assertEquals($pool->organisations_id, $pool->organisation->id);
    }

    public function test_pool_users_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $pool = Pool::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $users = User::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $pool->users()->sync(
            $users->map(fn($item) => $item->id)->toArray()
        );

        $this->assertEquals(10, $pool->users->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $pool->users);
        $this->assertInstanceOf(User::class, $pool->users->first());
    }
}
