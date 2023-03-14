<?php

namespace Tests\Unit\Listeners;

use App\Listeners\Email\CheckEnvironmentListener;
use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Symfony\Component\Mime\Email;

class CheckEnvironmentListenerTest extends TestCase
{

    public function test_is_attached_to_event()
    {
        Event::fake();
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $this->assertTrue(Event::hasListeners(MessageSending::class));
        Notification::assertSentTo($user, ResetPassword::class);
        Event::assertListening(MessageSending::class, CheckEnvironmentListener::class);
    }

    public function test_listener()
    {
        Event::fake();
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $listener = app()->make(CheckEnvironmentListener::class);
        $event = new MessageSending(new Email());
        $listener->handle($event);

        $this->assertEquals(strtoupper(env('APP_ENV')) . ': ', $event->message->getSubject());
    }
}
