<?php


use App\Actions\ActiveArchiveArchived;
use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Place\ActiveArchivedListener;
use App\Events\Place\ActiveArchived;
use App\Notifications\Place\ActiveArchived as ActiveArchivedNotification;

class ActiveArchiveArchivedTest extends TestCase
{
    public function test_action(): void
    {
        Event::fake();
        Notification::fake();
        $user = User::factory()->create();
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        $action = new ActiveArchiveArchived($place, $user);
        $action->execute();

        $listener = app()->make(ActiveArchivedListener::class);
        $event = new ActiveArchived($place, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($place->id, $event->place->id);
    }

}
