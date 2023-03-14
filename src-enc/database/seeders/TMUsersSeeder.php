<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;

class TMUsersSeeder extends Seeder
{
    public function run()
    {
        $organisationId = Organisation::whereNull('is_platform')->first()->id;
        User::factory(10)->agent()->create(['organisations_id' => $organisationId]);
        User::factory(5)->developer()->create(['organisations_id' => $organisationId]);
        User::factory(5)->admin()->unverified()->create(['organisations_id' => $organisationId]);
        User::factory(5)->admin()->superAdmin()->unverified()->create(['organisations_id' => $organisationId]);


        foreach (Organisation::factory(20)->create() as $organisation){
            User::factory(10)->agent()->create(['organisations_id' => $organisation->id]);
            User::factory(5)->developer()->create(['organisations_id' => $organisation->id]);
            $admins = User::factory(5)->admin()->unverified()->create(['organisations_id' => $organisation->id]);
            User::factory(5)->admin()->superAdmin()->unverified()->create(['organisations_id' => $organisation->id]);

            $organisation->created_by = $admins->first()->id;
            $organisation->save();
        }
    }
}
