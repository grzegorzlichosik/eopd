<?php

namespace Tests\Unit\Rules;

use App\Models\User;
use Tests\TestCase;
use App\Rules\CheckIfRoleIsDohOne;
use Illuminate\Foundation\Testing\WithFaker;

class CheckIfUserVerifiedTest extends TestCase
{
    use WithFaker;

    public function setup(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function test_rule_verified_user()
    {
        $user = User::factory()->create();

        $payload = [
            'email' => $this->faker->email,
            'roles' => ['is_agent']
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->put(route('admin.users.update', ['uuid' => $user->uuid]), $payload);

        $response->assertSessionHasErrors();

    }

    public function test_rule_unverified_user()
    {
        $user = User::factory()->unverified()->create();

        $payload = [
            'email' => $this->faker->email,
            'roles' => ['is_agent']
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->put(route('admin.users.update', ['uuid' => $user->uuid]), $payload);

        $response->assertSessionHasNoErrors();

    }

}
