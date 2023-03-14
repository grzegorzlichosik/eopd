<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DefaultOrganisationSeeder extends Seeder
{
    const ADMIN_EMAIL = "dev@threadable.io";

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('email', self::ADMIN_EMAIL)->first();

        if (!$admin) {

            $organisation = Organisation::create(
                [
                    'name'    => 'Threadable',
                    'phone_number'=>'1234567890',
                ]
            );

            User::create(
                [
                    'organisations_id'    => $organisation->id,
                    'email'               => self::ADMIN_EMAIL,
                    'password'            => Hash::make(Str::random(40)),
                    'name'                => 'Threadable DEV',
                    'email_verified_at'   => now(),
                    'password_updated_at' => now(),
                    'is_admin'            => 1,
                    'is_super_admin'      => 1,
                ]
            );
        }
    }
}
