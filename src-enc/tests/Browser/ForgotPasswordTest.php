<?php

namespace Tests\Browser;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ForgotPassword;
use Tests\DuskTestCase;

class ForgotPasswordTest extends DuskTestCase
{
    public function test_can_generate_reset_password_link()
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $reset = PasswordReset::where('email', $user->email);
        $this->assertNull($reset->first());

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new ForgotPassword())
                ->typeSlowly('@email', $user->email)
                ->click('@reset')
                ->pause(1000)
                ->assertSee(trans('passwords.sent'))
                ->assertPathIs((new ForgotPassword())->url());
        });
        $this->assertNotNull($reset->first());
    }

    public function test_can_not_generate_reset_password_link()
    {

        $email = "test@test.io";

        $reset = PasswordReset::where('email', $email);
        $this->assertNull($reset->first());

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit(new ForgotPassword())
                ->typeSlowly('@email', $email)
                ->click('@reset')
                ->pause(1000)
                ->assertSee(trans('passwords.sent'))
                ->assertPathIs((new ForgotPassword())->url());
        });
        $this->assertNull($reset->first());
    }
}
