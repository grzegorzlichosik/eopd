<?php

$theSelectedAttributeIsInvalid = 'The selected :attribute is invalid.';

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'accepted_if'          => 'The :attribute must be accepted when :other is :value.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => 'The :attribute must only contain letters.',
    'alpha_dash'           => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num'            => 'The :attribute must only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'array'   => 'The :attribute must have between :min and :max items.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute must be between :min and :max.',
        'string'  => 'The :attribute must be between :min and :max characters.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'current_password'     => 'The password is incorrect.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_equals'          => 'The :attribute must be a date equal to :date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'declined'             => 'The :attribute must be declined.',
    'declined_if'          => 'The :attribute must be declined when :other is :value.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'doesnt_end_with'      => 'The :attribute may not end with one of the following: :values.',
    'doesnt_start_with'    => 'The :attribute may not start with one of the following: :values.',
    'email'                => 'The :attribute must be a valid email address.',
    'ends_with'            => 'The :attribute must end with one of the following: :values.',
    'enum'                 => $theSelectedAttributeIsInvalid,
    'exists'               => $theSelectedAttributeIsInvalid,
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field must have a value.',
    'gt'                   => [
        'array'   => 'The :attribute must have more than :value items.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string'  => 'The :attribute must be greater than :value characters.',
    ],
    'gte'                  => [
        'array'   => 'The :attribute must have :value items or more.',
        'file'    => 'The :attribute must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'string'  => 'The :attribute must be greater than or equal to :value characters.',
    ],
    'image'                => 'The :attribute must be an image.',
    'in'                   => $theSelectedAttributeIsInvalid,
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'lt'                   => [
        'array'   => 'The :attribute must have less than :value items.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string'  => 'The :attribute must be less than :value characters.',
    ],
    'lte'                  => [
        'array'   => 'The :attribute must not have more than :value items.',
        'file'    => 'The :attribute must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'string'  => 'The :attribute must be less than or equal to :value characters.',
    ],
    'mac_address'          => 'The :attribute must be a valid MAC address.',
    'max'                  => [
        'array'   => 'The :attribute must not have more than :max items.',
        'file'    => 'The :attribute must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute must not be greater than :max.',
        'string'  => 'The :attribute must not be greater than :max characters.',
    ],
    'max_digits'           => 'The :attribute must not have more than :max digits.',
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'array'   => 'The :attribute must have at least :min items.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string'  => 'The :attribute must be at least :min characters.',
    ],
    'min_digits'           => 'The :attribute must have at least :min digits.',
    'multiple_of'          => 'The :attribute must be a multiple of :value.',
    'not_in'               => $theSelectedAttributeIsInvalid,
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'password'             => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present'              => 'The :attribute field must be present.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_array_keys'  => 'The :attribute field must contain entries for: :values.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values are present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'array'   => 'The :attribute must contain :size items.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'numeric' => 'The :attribute must be :size.',
        'string'  => 'The :attribute must be :size characters.',
    ],
    'starts_with'          => 'The :attribute must start with one of the following: :values.',
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid timezone.',
    'unique'               => 'The :attribute has already been taken.',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute must be a valid URL.',
    'uuid'                 => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes'                               => [],
    'password_rule'                            => 'The password must be at least :length characters and contain at least two uppercase characters, two lowercase characters and two numbers.',
    'must_be_email'                            => 'Email must be a valid email address.',
    'phone'                                    => 'Incorrect phone number.',
    'phone_required'                           => 'Phone number is required.',
    'please_select_role'                       => 'Please select user role.',
    'please_select_at_one_roles'               => 'Please select at least one user role.',
    'name_required'                            => 'Name is required.',
    'name_string'                              => 'Name must be a string.',
    'name_min'                                 => 'Name must be at least :min characters.',
    'name_max'                                 => 'Name must not be greater than :max characters.',
    'email_required'                           => 'Email is required.',
    'email_string'                             => 'Email must be a string.',
    'email_max'                                => 'Email must not be greater than :max characters.',
    'email_unique'                             => 'This email address has already been taken.',
    'roles_array'                              => 'Roles must be an array.',
    'attribute_cannot_be_update_user_verified' => 'The :attribute cannot be updated as user has already been verified.',
    'incorrect_roles'                          => 'Please select correct roles.',
    'pool_exists'                              => 'Pool with this name already exists.',
    'selected_users_required'                  => 'Please select at least one user.',
    'last_super_admin_remove_role'             => 'You are not able to remove Super Admin role from your account unless there is another Super Admin in your Organisation.',
    'last_super_admin_cannot_delete'           => 'You are not able to delete last Super Admin user from your Organisation. Please invite new user and set their role as Super Admin or promote any existing user within your Organisation to Super Admin role.',
    'postcode_integer'                         => 'Postcode must be a integer.',
    'city_town_string'                         => 'City / town must be a string.',
    'city_town_min'                            => 'City / town must be atleast :min characters.',
    'city_town_max'                            => 'City / town must be atleast :max characters.',
    'file_type'                                => 'File must be pdf.',
    'location_lat_required'                    => 'Location latitude is required.',
    'location_lon_required'                    => 'Location longitude is required.',
    'location_unique'                          => 'Location name must be unique.',
    'short_name_required'                      => 'Short name is required.',
    'short_name_string'                        => 'Short name must be a string.',
    'short_name_min'                           => 'Short name must be at least :min characters.',
    'short_name_max'                           => 'Short name must not be greater than :max characters.',
    'short_name_unique'                        => 'Short name must be unique.',
    'selected_agents_required'                 => 'Please select at least one agent.',
    'channels_required_array_keys'             => 'Please select at least one channel.',
    'objective_required'                       => 'Objective is required.',
    'objective_string'                         => 'Objective must be a string.',
    'objective_min'                            => 'Objective must be at least :min characters.',
    'objective_max'                            => 'Objective must not be greater than :max characters.',
    'selected_flows_required'                  => 'Please select at least one place.',
    'file_required'                            => 'File is required.',
    'address_required'                         => 'Address is required.',
    'description_required'                     => 'Description is required.',
    'description_min'                          => 'Description must be at least :min characters.',
    'description_max'                          => 'Description must must not be greater than :max characters.',
    'type_required'                            => 'Please select place type.',
    'status_required'                          => 'Please select status.',
    'location_required'                        => 'Please select location.',
    'timezone_required'                        => 'Timezone is required.',
    'image_type'                               => 'File must be jpg, jpeg or png format.',
    'organisation_exists'                      => 'Organisation name already exists.',
];
