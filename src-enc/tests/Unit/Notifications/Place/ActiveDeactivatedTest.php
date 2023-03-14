<?php

namespace tests\Unit\Notifications\Place;

use App\Models\User;
use App\Models\Place;
use App\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\Place\ActiveDeactivated;

class ActiveDeactivatedTest extends TestCase
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
            new ActiveDeactivated($place)
        );

        $testData = [
            'subject'    => 'Active place has been deactivated',
            'salutation' => BaseNotification::getSalutation(),
            'line1'      => 'Place name: ' . $place->name,
        ];

        Notification::assertSentTo(
            $user,
            ActiveDeactivated::class,
            function ($notification, $channels, $notifiable) use ($testData, $place) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals('Active place has been deactivated', $mailData['subject']);
                $this->assertEquals('Hello ' . $notifiable['name'] . ',', $mailData['greeting']);
                $this->assertEquals('Place name: ' . $place->name, $mailData['introLines'][0]);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);

                return true;
            });
    }
}
