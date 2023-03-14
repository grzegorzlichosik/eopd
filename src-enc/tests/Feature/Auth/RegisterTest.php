<?php

namespace Tests\Feature\Auth;

use App\Models\Organisation;
use App\Models\User;
use App\Notifications\NewUserInvite;
use App\Rules\MustBeValidEmail;
use App\Rules\MustBeValidPhone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Jetstream\Jetstream;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    protected const NAME = "name";
    protected const EMAIL = "name@email.com";
    protected const PHONE_NUMBER = "9876543210";
    protected const ORGANISATION_NAME = "organisation name";
    protected const DIALCODE = '91';
    protected const COUNTRYCODE = 'IN';
    private const TOKEN = "740c150368bd214ae0d5815cd8e4791583751b8d5cc920fb43988a3da924613f";

    public function test_register_screen_is_rendered()
    {
        $response = $this->get('/register',['preferredCountries'=>['US'|'IE']]);

        $response->assertStatus(200);
    }

    public function test_request_should_fail_when_no_name_is_provided()
    {
        $response = $this->withoutMiddleware()->postJson(route('register'), [
            "email" => self::EMAIL,
            "country_code" => self::COUNTRYCODE,
            "dial_code" => self::DIALCODE,
            "phone_number" => self::PHONE_NUMBER,
            "organisation_name" => self::ORGANISATION_NAME,
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('name');
    }

    public function test_request_should_fail_when_no_email_is_provided()
    {
        $response = $this->withoutMiddleware()->postJson(route('register'), [
            "name" => self::NAME,
            "country_code" => self::COUNTRYCODE,
            "dial_code" => self::DIALCODE,
            "phone_number" => self::PHONE_NUMBER,
            "organisation_name" => self::ORGANISATION_NAME,
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('email');
    }

    public function test_request_should_fail_when_no_phone_number_is_provided()
    {
        $response = $this->withoutMiddleware()->postJson(route('register'), [
            "name" => self::NAME,
            "email" => self::EMAIL,
            "country_code" => '',
            "dial_code" => self::DIALCODE,
            "organisation_name" => self::ORGANISATION_NAME,
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('phone_number');
    }

    public function test_request_should_fail_when_no_organisation_name_is_provided()
    {
        $response = $this->withoutMiddleware()->postJson(route('register'), [
            "name" => self::NAME,
            "email" => self::EMAIL,
            "country_code" => self::COUNTRYCODE,
            "dial_code" => self::DIALCODE,
            "phone_number" => self::PHONE_NUMBER,
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('organisation_name');
    }

    public function test_request_should_succeed_when_all_data_is_provided()
    {
        $response = $this->withoutMiddleware()->postJson(route('register'), [
            "name" => self::NAME,
            "email" => self::EMAIL,
            "country_code" => self::COUNTRYCODE,
            "dial_code" => self::DIALCODE,
            "phone_number" => self::PHONE_NUMBER,
            "organisation_name" => self::ORGANISATION_NAME,
        ]);

        $response->assertStatus(
            Response::HTTP_OK
        );
        $this->assertDatabaseHas('organisations', [
            "name" => self::ORGANISATION_NAME,
        ]);
        $this->assertDatabaseHas('users', [
            "name" => self::NAME,
            "email" => self::EMAIL,
        ]);
        $user = User::where('email', self::EMAIL)->first();

        $this->assertDatabaseHas('organisations', [
            "created_by" => $user->id,
        ]);

        $user->notify(new NewUserInvite(self::TOKEN));
        $this->get('register')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Auth/Register')
            );
    }
}
