<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Flow;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Validators\AddFlowPlaceValidator;
use Illuminate\Support\Facades\DB;

class FlowResourceController extends Controller
{

    private const ROUTE = 'admin.flows.resources';

    public function __construct(
        private readonly AddFlowPlaceValidator $addFlowPlaceValidator
    )
    {
    }


    public function store(Request $request, string $uuid): RedirectResponse
    {
        $request = $request->all();
        $flow = Flow::flowDetails($uuid);
        $this->addFlowPlaceValidator->validate($request);

        try {
            $selectedAgentsUuids = $request['selected_flows'];
            foreach ($selectedAgentsUuids as $value) {
                $uuid = $value['uuid'];
                $channel = Channel::where('uuid', $value['channelUuid'])->first();

                Place::where('organisations_id', auth()->user()->organisations_id)
                    ->whereUuidIn([$uuid])
                    ->get()
                    ->each(function ($place) use ($channel) {
                        $channel->places()->attach($place->id);
                    });
            }

            return redirect(route(self::ROUTE, ['uuid' => $flow->uuid]), 303)
                ->with('toaster', [
                        'message' => trans('modals.flow_place', ['flow' => $flow->name])
                    ]
                );
        } catch (\Exception $e) {
            return redirect(route(self::ROUTE, ['uuid' => $flow->uuid]), 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }


    public function destroy(string $uuid, string $place, string $channel): RedirectResponse
    {
        $flow = Flow::where('uuid', $uuid)->first();

        try {
            $channel = Channel::where('uuid', $channel)->firstorFail();
            $place = Place::where('uuid', $place)->firstorFail();

            $place->channels()->detach($channel->id);

            return redirect(route(self::ROUTE, ['uuid' => $uuid]), 303)
                ->with('toaster', [
                        'message' => trans('modals.flow_place_delete', ['flow' => $flow->name])
                    ]
                );
        } catch (\Exception $e) {
            return redirect(route(self::ROUTE, ['uuid' => $flow->uuid]), 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    public function searchPlace(Request $request, string $uuid): JsonResponse
    {
        $request = $request->all();
        try {
            $flow = Flow::flowDetails($uuid);
            $channelsId = null;
            $channels = [];
            foreach ($flow->channels as $flowChannel) {
                if ($flowChannel->channel_types_id === ChannelType::F2F) {
                    $channelsId = $flowChannel->id;
                    $channels = Channel::where('id', $channelsId)->first();
                }
            }
            $result = Place::where('organisations_id', auth()->user()->organisations_id)
                ->where('name', 'LIKE', '%' . $request['search'] . '%')
                ->withAggregate('location', 'name');
            if (isset($request['selected'])) {
                $result = $result->whereNotIn('uuid', $request['selected']);
            }
            $result = $result->whereDoesntHave('channels', function (Builder $query) use ($channelsId) {
                $query->where('channels_id', $channelsId);
            })
                ->get();

            $places = [];
            foreach ($result as $value) {
                $places[] = array(
                    "uuid"        => $value->uuid,
                    "channelUuid" => $channels->uuid,
                    "name"        => $value->name . ' (' . $value->location_name . ')'
                );
            }

            return response()->json($places);
        } catch (\Throwable $e) {
            return response()->json(getErrorMessage($e));
        }
    }

}
