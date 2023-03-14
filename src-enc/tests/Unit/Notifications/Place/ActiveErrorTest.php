<?php

namespace tests\Unit\Notifications\Place;

use App\Models\User;
use App\Models\Place;
use App\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\Place\ActiveError;

class ActiveErrorTest extends TestCase
{
    public function test_notification(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        $user = User::factory()->create();
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        Notification::send(
            $user,
            new ActiveError($place)
        );

        $testData = [
            'subject'    => 'Resourced active place has been updated with error',
            'salutation' => BaseNotification::getSalutation(),
            'line1'      => 'Place name: ' . $place->name,
        ];

        Notification::assertSentTo(
            $user,
            ActiveError::class,
            function ($notification, $channels, $notifiable) use ($testData, $place) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals('Resourced active place has been updated with error', $mailData['subject']);
                $this->assertEquals('Hello ' . $notifiable['name'] . ',', $mailData['greeting']);
                $this->assertEquals('Place name: ' . $place->name, $mailData['introLines'][0]);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);

                return true;
            });
    }
}
