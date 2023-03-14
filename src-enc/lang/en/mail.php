<?php

return [
    'reset_password_subject' => 'Reset Password Notification',
    'reset_password_line_1'  => 'You are receiving this email because we received a password reset request for your account. Please click the below button to reset your password.',
    'reset_password_line_2'  => 'This password reset link will expire in :count minutes.',
    'reset_password_line_3'  => 'If you did not request a password reset, no further action is required.',
    'reset_password_cta'     => 'Reset Password',

    'account_activation_subject' => 'Account Activation',
    'account_activation_line1'   => 'Welcome aboard. To activate your new account, please click the button below.',
    'account_activation_line2'   => 'This link will expire in :count minutes. If you did not register for an account with WhenCounter, please ignore this email.',
    'account_activation_cta'     => 'Activate my account',

    'reset_2fa_subject' => 'Reset Password and 2FA credentials',
    'reset_2fa_line1'   => 'You are receiving this email because we received a 2FA reset request for your account. Please click the below button to reset your password and setup your 2 Factor Authentication details.',
    'reset_2fa_line_2'  => 'This password reset link will expire in :count minutes.',
    'reset_2fa_cta'     => 'Reset Password',


    'recovery_code_subject' => '2FA Recovery Code',
    'recovery_code_line_1'  => 'Please see the recovery codes for your two factor authentication attached below. You can use them to recover access to your account when your two factor authentication device has been lost.',

    'invite_new_user_subject' => ':admin has invited you to ' . env('APP_NAME'),
    'invite_new_user_line_1'  => 'You have been requested to join our organisation ":organisation" to collaborate on ' . env('APP_NAME'),
    'invite_new_user_line_2'  => 'To activate your new account, please click the button below.',
    'invite_new_user_line_3'  => 'This link will expire in :count minutes. You can request a new link by following the instructions at the link above.',
    'invite_new_user_cta'     => 'Activate my account.',

    'event_details'      => 'Event Details',
    'scheduled_date'     => 'Scheduled Date',
    'start_time'         => 'Start time',
    'end_time'           => 'End time',
    'channel'            => 'Channel',
    'max_participants'   => 'Max Participants',
    'requestor'          => 'Requestor',
    'requestor_details'  => 'Requestor Details',
    'phone_number'       => 'Phone number',
    'email'              => 'Email',
    'name'               => 'Name',
    'cancel_booking'     => 'Cancel Booking',
    'reschedule_booking' => 'Reschedule Booking',
    'add_participants'   => 'Add Participants',

    'encounter_agent_line_1' => 'Encounter for :title scheduled on :date from :start to :end has been booked successfully.',
    'cancel_line_1'          => 'Encounter for :title scheduled on :date from :start to :end has been canceled.',
    'reschedule_line_1'      => 'Encounter for :title has been rescheduled to :date from :start to :end successfully.',
];
