<?php


use App\Actions\ActiveDeactivateInactive;
use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Place\ActiveDeactivatedListener;
use App\Events\Place\ActiveDeactivated;
use App\Notifications\Place\ActiveDeactivated as ActiveDeactivatedNotification;

class ActiveDeactivateInactiveTest extends TestCase
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

        $action = new ActiveDeactivateInactive($place, $user);
        $action->execute();

        $listener = app()->make(ActiveDeactivatedListener::class);
        $event = new ActiveDeactivated($place, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($place->id, $event->place->id);
    }

}
