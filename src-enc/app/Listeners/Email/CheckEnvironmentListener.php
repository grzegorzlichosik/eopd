<?php

namespace App\Listeners\Email;

class CheckEnvironmentListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        if (env('APP_ENV') != 'production') {
            $event->message->subject(strtoupper(env('APP_ENV')) . ': ' . $event->message->getSubject());
        }
    }
}
