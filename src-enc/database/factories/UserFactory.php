<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid'              => Str::uuid()->toString(),
            'organisations_id'  => Organisation::factory(),
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified(): UserFactory
    {
        return $this->state(function () {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function isLocked(): UserFactory
    {
        return $this->state(function () {
            return [
                'locked_at' => now(),
            ];
        });
    }

    public function requiresTwoFactorReset(): UserFactory
    {
        return $this->state(function () {
            return [
                'two_factor_reset_request_at' => now(),
            ];
        });
    }

    public function requiresPasswordUpdate(): UserFactory
    {
        return $this->state(function () {
            return [
                'password_updated_at' => now()->subDays(180),
            ];
        });
    }

    public function maxFailedLogins(): UserFactory
    {
        return $this->state(function () {
            return [
                'failed_logins' => 5,
            ];
        });
    }

    public function admin(): UserFactory
    {
        return $this->state(function () {
            return [
                'is_admin' => 1,
            ];
        });
    }

    public function superAdmin(): UserFactory
    {
        return $this->state(function () {
            return [
                'is_super_admin' => 1,
            ];
        });
    }

    public function agent(): UserFactory
    {
        return $this->state(function () {
            return [
                'is_agent' => 1,
            ];
        });
    }

    public function developer(): UserFactory
    {
        return $this->state(function () {
            return [
                'is_developer' => 1,
            ];
        });
    }

    public function twoFactorConfirmed(): UserFactory
    {
        return $this->state(function () {
            return [
                'two_factor_confirmed_at'   => now(),
                'two_factor_secret'         => encrypt('123456'),
                'two_factor_recovery_codes' => encrypt(json_encode(['123456'])),
            ];
        });
    }

    public function twoFactorNotConfirmed()
    {
        return $this->state(function () {
            return [
                'two_factor_confirmed_at'   => null,
                'two_factor_secret'         => null,
                'two_factor_recovery_codes' => null,
            ];
        });
    }
}
