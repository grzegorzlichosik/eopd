<?php

namespace App\Actions\Fortify;

use App\Models\Organisation;
use App\Models\User;
use App\Rules\MustBeValidEmail;
use App\Rules\MustBeValidPhone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    protected const RULE = 'max:255';
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', self::RULE],
            'email' => ['required', 'string', 'email', new MustBeValidEmail, self::RULE, 'unique:users'],
            'phone_number' => ['required', 'phone:' . $input['country_code']],
            'organisation_name' => ['required', 'string', self::RULE, 'min:3'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $organisation = Organisation::create([
            'name'         => $input['organisation_name'],
            'phone_number' => '+'.$input['dial_code'].' '.$input['phone_number']
        ]);

        $user = User::create([
            'name'                => $input['name'],
            'email'               => $input['email'],
            'organisations_id'    => $organisation->id,
            'password'            => Hash::make(Str::random(12)),
            'password_updated_at' => now(),
            'is_admin'            => 1,
            'is_super_admin'      => 1,
        ]);

        $organisation->created_by = $user->id;
        $organisation->save();

        return $user;
    }
}
