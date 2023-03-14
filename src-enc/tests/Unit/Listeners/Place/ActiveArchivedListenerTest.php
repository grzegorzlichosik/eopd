<?php

namespace Tests\Unit\Listeners\Place;

use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Place\ActiveArchivedListener;
use App\Events\Place\ActiveArchived;
use App\Notifications\Place\ActiveArchived as ActiveArchivedNotification;

class ActiveArchivedListenerTest extends TestCase
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

        $listener = app()->make(ActiveArchivedListener::class);
        $event = new ActiveArchived($place, $user);
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

        $listener = app()->make(ActiveArchivedListener::class);
        $event = new ActiveArchived($place, $user);
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            ActiveArchivedNotification::class,
            function ($notification, $channels) use ($place) {
                return $notification->place->id === $place->id;
            }
        );
    }
}
