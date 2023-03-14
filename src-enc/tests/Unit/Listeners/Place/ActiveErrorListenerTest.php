<?php

namespace Tests\Unit\Listeners\Place;

use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Place\ActiveErrorListener;
use App\Events\Place\ActiveError;
use App\Notifications\Place\ActiveError as ActiveErrorNotification;

class ActiveErrorListenerTest extends TestCase
{
    public function test_listener(): void
    {
        Event::fake();
        $user = User::factory()->create();
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        $listener = app()->make(ActiveErrorListener::class);
        $event = new ActiveError($place, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($place->id, $event->place->id);
    }

    public function test_listener_notification(): void
    {
        Event::fake();
        Notification::fake();
        $user = User::factory()->admin()->create();
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        $listener = app()->make(ActiveErrorListener::class);
        $event = new ActiveError($place, $user);
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            ActiveErrorNotification::class,
            function ($notification, $channels) use ($place) {
                return $notification->place->id === $place->id;
            }
        );
    }
}
