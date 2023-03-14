<?php

namespace App\Providers;

use App\Events\Place\ActiveArchived as PlaceActiveArchived;
use App\Events\Place\ActiveDeactivated as PlaceActiveDeactivated;
use App\Events\Place\ActiveError as PlaceActiveError;
use App\Listeners\Encounter\PlaceEncounterAutoRescheduleListener;
use App\Listeners\Place\ActiveArchivedListener as PlaceActiveArchivedListener;
use App\Listeners\Place\ActiveDeactivatedListener as PlaceActiveDeactivatedListener;
use App\Listeners\Place\ActiveErrorListener as PlaceActiveErrorListener;


use App\Events\Encounter\PlaceEncounterAutoReschedule;


use App\Events\Flow\FlowDraftArchiveArchived;
use App\Events\Flow\FlowDraftEditDraft;
use App\Events\Flow\FlowDraftPublishPublished;
use App\Events\Flow\FlowInactiveArchiveArchived;
use App\Events\Flow\FlowInactiveEditInactive;
use App\Events\Flow\FlowInactivePublishPublished;
use App\Events\Flow\FlowInitialCreateDraft;
use App\Events\Flow\FlowPublishedArchiveArchived;
use App\Events\Flow\FlowPublishedEditPublished;
use App\Events\Flow\FlowPublishedSuspendInactive;
use App\Listeners\Flow\FlowDraftArchiveArchivedListener;
use App\Listeners\Flow\FlowDraftEditDraftListener;
use App\Listeners\Flow\FlowDraftPublishPublishedListener;
use App\Listeners\Flow\FlowInactiveArchiveArchivedListener;
use App\Listeners\Flow\FlowInactiveEditInactiveListener;
use App\Listeners\Flow\FlowInactivePublishPublishedListener;
use App\Listeners\Flow\FlowInitialCreateDraftListener;
use App\Listeners\Flow\FlowPublishedArchiveArchivedListener;
use App\Listeners\Flow\FlowPublishedEditPublishedListener;
use App\Listeners\Flow\FlowPublishedSuspendInactiveListener;

use App\Listeners\Email\CheckEnvironmentListener;
use App\Listeners\UserLoginListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Login::class                        => [
            UserLoginListener::class
        ],

        /***********************************************************
         *          EMAIL SENDING EVENTS
         ***********************************************************/
        MessageSending::class               => [
            CheckEnvironmentListener::class,
        ],

        /***********************************************************
         *          FLOWS TRANSIT EVENTS
         ***********************************************************/
        FlowDraftArchiveArchived::class     => [
            FlowDraftArchiveArchivedListener::class,
        ],
        FlowDraftEditDraft::class           => [
            FlowDraftEditDraftListener::class,
        ],
        FlowDraftPublishPublished::class    => [
            FlowDraftPublishPublishedListener::class,
        ],
        FlowInactiveArchiveArchived::class  => [
            FlowInactiveArchiveArchivedListener::class,
        ],
        FlowInactiveEditInactive::class     => [
            FlowInactiveEditInactiveListener::class,
        ],
        FlowInactivePublishPublished::class => [
            FlowInactivePublishPublishedListener::class,
        ],
        FlowInitialCreateDraft::class       => [
            FlowInitialCreateDraftListener::class,
        ],
        FlowPublishedArchiveArchived::class => [
            FlowPublishedArchiveArchivedListener::class,
        ],
        FlowPublishedEditPublished::class   => [
            FlowPublishedEditPublishedListener::class,
        ],
        FlowPublishedSuspendInactive::class => [
            FlowPublishedSuspendInactiveListener::class,
        ],

        /***********************************************************
         *          PLACES TRANSIT EVENTS
         ***********************************************************/
        PlaceActiveArchived::class    => [
            PlaceActiveArchivedListener::class,
        ],
        PlaceActiveDeactivated::class => [
            PlaceActiveDeactivatedListener::class,
        ],
        PlaceActiveError::class       => [
            PlaceActiveErrorListener::class,
        ],

        /***********************************************************
         *          PLACE ENCOUNTER TRANSIT EVENTS
         ***********************************************************/
        PlaceEncounterAutoReschedule::class => [
            PlaceEncounterAutoRescheduleListener::class,
        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
