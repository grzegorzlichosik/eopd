<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlatformOrganisationSeeder extends Seeder
{
    const ADMIN_EMAIL = "dev+platform@threadable.io";

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $organisation = Organisation::where('is_platform', 1)->first();

        if (!$organisation) {

            $organisation = new Organisation;
            $organisation->name = 'Platform Organisation';
            $organisation->is_platform = 1;
            $organisation->phone_number = '';
            $organisation->save();

            User::create(
                [
                    'organisations_id'    => $organisation->id,
                    'email'               => self::ADMIN_EMAIL,
                    'password'            => Hash::make(Str::random(40)),
                    'name'                => 'Platform Threadable DEV',
                    'email_verified_at'   => now(),
                    'password_updated_at' => now(),
                    'is_admin'            => 1,
                    'is_super_admin'      => 1,
                ]
            );
        }
    }
}
