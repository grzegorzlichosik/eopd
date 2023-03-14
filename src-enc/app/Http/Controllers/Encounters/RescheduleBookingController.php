<?php

namespace App\Http\Controllers\Encounters;

use App\Models\Encounter;
use App\Models\EncounterAttendee;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Encounter\RescheduleService;
use App\Validators\BookEncounterAddParticipantsValidator;

class RescheduleBookingController extends EncounterController
{

    public const ROUTE = 'Encounters/Response';

    public function __construct(
        protected RescheduleService                     $service,
        protected BookEncounterAddParticipantsValidator $addParticipantsValidator
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function index(string $uuid): \Inertia\Response
    {
        try {
            $return = [
                'event' => $this->encounterDetails($uuid)
            ];

        } catch (ModelNotFoundException $e) {
            report($e);
            $return = [
                'backendError' => trans('bookings.no_event_found'),
                'event'        => null
            ];
        } catch (\Exception $e) {
            report($e);
            $return = [
                'backendError' => getErrorMessage($e),
                'event'        => null
            ];
        }

        return Inertia::render('Encounters/RescheduleBooking', $return);
    }

    /**
     * @codeCoverageIgnore
     */
    public function create(string $uuid): \Inertia\Response
    {
        $event = $this->encounterDetails($uuid);
        $max = $event->channel?->max_participants;
        $maxAttendees = $max - $event->attendees()->count();

        return Inertia::render(
            'Encounters/AddParticipants',
            [
                'event'           => $event,
                'maxParticipants' => $max ? $maxAttendees : 0,
            ]
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function update(string $uuid, Request $request): JsonResponse
    {
        try {
            $encounter = $this->encounterDetails($uuid);

            if ($encounter->scheduled_at < now() || $encounter->tsm_current_state !== Encounter::STATE_SCHEDULED) {
                throw new ModelNotFoundException();
            }

            $this->service->rescheduleBooking($encounter, $request->all());

            return response()->json(
                [
                    'status'  => 'success',
                    'message' => trans('bookings.booking_rescheduled_successfully'),
                    'event'   => $this->encounterDetails($uuid),
                ]
            );

        } catch (ModelNotFoundException $e) {
            report($e);
            $message = trans('bookings.no_event_found');
        } catch (\Exception $e) {
            report($e);
            $message = getErrorMessage($e);
        }

        return response()->json(
            [
                'status'  => 'error',
                'message' => $message
            ],
            422
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function store(string $uuid, Request $request)
    {
        $request = $request->all();
        $this->addParticipantsValidator->validate($request);

        $encounter = $this->encounterDetails($uuid);

        if ($encounter->scheduled_at < now() || $encounter->tsm_current_state !== Encounter::STATE_SCHEDULED) {
            throw new ModelNotFoundException();
        }

        $this->service->addAttendees($encounter, $request);

        return Inertia::render(
            'Encounters/AddParticipants',
            [
                'event' => $encounter,
            ]
        );
    }

}
