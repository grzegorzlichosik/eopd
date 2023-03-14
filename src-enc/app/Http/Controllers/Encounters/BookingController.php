<?php

namespace App\Http\Controllers\Encounters;

use App\Exceptions\EncounterBookingException;
use App\Exceptions\OAuthConnectionException;
use App\Models\ChannelType;
use App\Services\OAuth\OAuthNylasService;
use App\Validators\BookEncounterValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use App\Services\Encounter\BookingService;

/**
 * @codeCoverageIgnore
 */
class BookingController extends EncounterController
{

    private const ERRORS_MISSING_FLOW = 'errors.missing_flow';

    public function __construct(
        protected OAuthNylasService $oAuthService,
        protected BookingService    $bookingService,
        private                     readonly BookEncounterValidator $bookEncounterValidator
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function postShow(Request $request): Response|InertiaResponse
    {
        $request = $request->all();
        if (!empty($request['uuid'])) {
            return $this->show($request['uuid'], $request);
        }
        return response(trans(self::ERRORS_MISSING_FLOW));
    }

    /**
     * @codeCoverageIgnore
     */
    public function getShow(string $uuid, Request $request): Response|InertiaResponse
    {
        return $this->show($uuid, $request->all(), 'GetBooking');
    }

    /**
     * @codeCoverageIgnore
     */
    private function show(string $uuid, array $request, string $component = 'PostBooking'): Response|InertiaResponse
    {
        $flow = $this->getFlow($uuid);
        if (!$flow || $flow->channels->isEmpty()) {
            return response(trans(self::ERRORS_MISSING_FLOW));
        }

        collect($flow->channels)->map(function ($channel) {
            $channel->type_name = trans('channels.' . $channel->type->label);
            $channel->is_phone_required = $channel->type->id === ChannelType::PHONE;
            return $channel;
        });

        return Inertia::render(
            'Encounters/' . $component,
            [
                'flow'               => $flow,
                'preferredCountries' => explode("|", env('PREFERRED_COUNTRIES', "US|IE|GB")),
                'request'            => $request,
            ]
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function store(string $uuid, Request $request): \Illuminate\Http\RedirectResponse
    {

        $request = $request->all();
        $this->bookEncounterValidator->validate($request);

        try {
            $flow = $this->getFlow($uuid);
            if (!$flow) {
                throw new EncounterBookingException(trans(self::ERRORS_MISSING_FLOW));
            }

            $channel = $flow->channels()->where('uuid', $request['channel'])->with('type:id,name')->first();
            $event = $this->bookingService->makeBooking($flow, $channel, $request);

            return back()
                ->with('toaster', [
                        'message' => trans('modals.event_create_successfully', ['event' => $event->uuid])
                    ]
                );

        } catch (EncounterBookingException|OAuthConnectionException $e) {
            report($e);
            throw ValidationException::withMessages([
                'attendee_name' => getErrorMessage($e)
            ]);
        }
    }
}
