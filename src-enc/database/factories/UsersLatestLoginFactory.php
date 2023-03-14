<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UsersLatestLogin;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsersLatestLoginFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UsersLatestLogin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'users_id' => User::factory(),
            'ip'       => $this->faker->ipv4(),
        ];
    }
}
