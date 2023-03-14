<?php

namespace App\Http\Controllers\Encounters;

use App\Models\Encounter;
use App\Services\Encounter\CancelService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class CancelBookingController extends EncounterController
{
    public function __construct(
        protected CancelService $cancelService
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function cancel(string $uuid): \Inertia\Response
    {
        try {
            $return = [
                'event' => $this->encounterDetails($uuid)
            ];

        } catch (ModelNotFoundException $e) {
            $return = [
                'backendError' => trans('bookings.no_event_found'),
                'event'        => null
            ];
        } catch (\Exception $e) {
            $return = [
                'backendError' => getErrorMessage($e),
                'event'        => null
            ];
        }

        return Inertia::render('Encounters/CancelBooking', $return);
    }

    /**
     * @codeCoverageIgnore
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $encounter = $this->encounterDetails($uuid);

            if ($encounter->scheduled_at < now() || $encounter->tsm_current_state !== Encounter::STATE_SCHEDULED) {
                throw new ModelNotFoundException();
            }

            $this->cancelService->cancelBooking($encounter);

            return response()->json(
                [
                    'status'  => 'success',
                    'message' => trans('bookings.booking_cancelled_successfully'),
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
}
