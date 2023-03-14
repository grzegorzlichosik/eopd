<?php

namespace App\Http\Controllers\Encounters;

use App\Models\ChannelType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Services\Encounter\AvailabilityService;

/**
 * @codeCoverageIgnore
 */
class AvailabilityController extends EncounterController
{
    public function __construct(
        protected AvailabilityService $availabilityService,
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function index(string $uuid, Request $request): JsonResponse
    {
        $request = $request->all();
        $flow = $this->getFlow($uuid);
        $channel = $this->getChannel($request['channel_uuid'], $flow);
        $date = Carbon::parse($request['date']);

        $channel->is_phone_required = $channel->type->id === ChannelType::PHONE;

        return response()->json($this->availabilityService->getAvailability($flow, $channel, $date));
    }

}
