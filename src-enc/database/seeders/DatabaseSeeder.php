<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new PlatformOrganisationSeeder())->run();
        (new DefaultOrganisationSeeder())->run();
        (new PlaceTypesSeeder())->run();
        (new ChannelTypesSeeder())->run();
        (new BookingSetupSeeder())->run();
        (new PopulateTimezoneSeeder())->run();
    }
}
