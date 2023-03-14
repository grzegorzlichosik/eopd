<?php


use App\Actions\ActiveNylasUpdateErrorError;
use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Place\ActiveErrorListener;
use App\Events\Place\ActiveError;

class ActiveNylasUpdateErrorErrorTest extends TestCase
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

        $action = new ActiveNylasUpdateErrorError($place, $user);
        $action->execute();

        $listener = app()->make(ActiveErrorListener::class);
        $event = new ActiveError($place, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($place->id, $event->place->id);
    }

}
