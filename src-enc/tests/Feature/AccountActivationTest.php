<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewUserInvite;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;

class AccountActivationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    private const TOKEN = "740c150368bd214ae0d5815cd8e4791583751b8d5cc920fb43988a3da924613f";

    public function setup(): void
    {
        parent::setUp();
    }

    public function test_account_activation_screen_can_be_rendered()
    {
        $user = User::factory()->unverified()->create([]);

        $response = $this->actingAs($user)->get('/resend-activation');

        $response->assertStatus(200);
    }

    public function test_resend_account_activation_screen_can_be_rendered()
    {
        $user = User::factory()->unverified()->create([]);

        $response = $this->withoutMiddleware()->post('/resend-activation', [
            'email' => $user->email
        ]);

        $user->notify(new NewUserInvite(self::TOKEN));
        $response->assertStatus(200);
    }

}
