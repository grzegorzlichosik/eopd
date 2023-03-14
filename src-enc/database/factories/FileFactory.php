<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        return [
            'uuid'             => Str::uuid(),
            'organisations_id' => $organisation->id,
            'users_id'         => $user->id,
            'name'             => Str::random() . '.pdf',
            'mimetype'         => 'application/pdf',
            'size'             => random_int(100000, 999999),
        ];
    }
}
