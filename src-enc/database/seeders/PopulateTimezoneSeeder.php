<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;

class PopulateTimezoneSeeder extends Seeder
{
    public function run()
    {
        Location::whereNull('timezone')
            ->get()
            ->each(function ($location) {
                $location->timezone = 'UTC';
                $location->save();
            });


        User::whereNull('timezone')
            ->get()
            ->each(function ($user) {
                $user->timezone = 'UTC';
                $user->save();
            });


    }
}
