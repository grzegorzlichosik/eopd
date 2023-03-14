<?php

namespace Tests\Unit\Listeners\Place;

use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Place\ActiveDeactivatedListener;
use App\Events\Place\ActiveDeactivated;
use App\Notifications\Place\ActiveDeactivated as ActiveDeactivatedNotification;

class ActiveDeactivatedListenerTest extends TestCase
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

        $listener = app()->make(ActiveDeactivatedListener::class);
        $event = new ActiveDeactivated($place, $user);
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

        $listener = app()->make(ActiveDeactivatedListener::class);
        $event = new ActiveDeactivated($place, $user);
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            ActiveDeactivatedNotification::class,
            function ($notification, $channels) use ($place) {
                return $notification->place->id === $place->id;
            }
        );
    }
}
