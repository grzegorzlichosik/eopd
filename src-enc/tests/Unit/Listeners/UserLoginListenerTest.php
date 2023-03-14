<?php

namespace Tests\Unit\Listeners;

use App\Listeners\Email\CheckEnvironmentListener;
use App\Listeners\UserLoginListener;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserLoginListenerTest extends TestCase
{

    public function test_is_attached_to_event()
    {
        Event::fake();
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertTrue(Event::hasListeners(Login::class));
        Event::assertListening(Login::class, UserLoginListener::class);
    }

    public function test_listener()
    {
        Event::fake();
        $user = User::factory()->create();

        $listener = app()->make(UserLoginListener::class);
        $event = new Login('web', $user, false);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($user->last_login_at, $event->user->last_login_at);
        $this->assertNull($user->two_factor_reset_request_at);

        $user->load('latestLogin');
        $this->assertNull($user->latestLogin->first());
    }
}
