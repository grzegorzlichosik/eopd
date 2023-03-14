<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Pool;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Validators\UpdateFlowValidator;
use App\Validators\CreateFlowValidator;
use PHPUnit\Util\Exception;


class FlowController extends Controller
{
    private const ROUTE = 'admin.flows.agents';

    public function __construct(
        private readonly UpdateFlowValidator $updateFlowValidator,
        private readonly CreateFlowValidator $createFlowValidator
    )
    {
    }

    public function index(): \Inertia\Response
    {
        list($sortField, $sortOrder) = getTableSorting();

        $flows = Flow::where('organisations_id', auth()->user()->organisations_id)
            ->withCount(['channels', 'users', 'encounters'])
            ->with('channels.places')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    return $q->orWhere('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(20)
            ->appends(cleanupAppends(request()->input()));

        collect($flows->items())->map(function ($flow) {
            $places = [];
            if ($flow->channels->isNotEmpty()) {
                foreach ($flow->channels as $channel) {
                    foreach ($channel->places as $place) {
                        $places[] = $place->id;
                    }
                }
            }

            $flow->places_count = count(array_unique($places));
            return $flow;
        });


        return Inertia::render('Admin/Flows', [
            'flows'        => $flows,
            'channelTypes' => ChannelType::get()
                ->map(function ($item) {
                    return
                        [
                            'name' => trans('channels.' . $item['label']),
                            'uuid' => $item['uuid']
                        ];
                }),
            'searchValue'  => request()->input('search'),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->createFlowValidator->validate($request);
        try {
            DB::beginTransaction();

            $flow = Flow::create([
                'organisations_id' => auth()->user()->organisations_id,
                'name'             => $request['name'],
                'objective'        => $request['objective']
            ]);

            $flow->transit('flow_create');

            $channels = [];
            foreach ($request['channels'] as $channel) {
                $channels[] = [
                    'organisations_id' => auth()->user()->organisations_id,
                    'channel_types_id' => $this->getChannelType($channel['channelTypeUuid']),
                    'max_participants' => $channel['max_participants'] ?? 0,
                    'is_auto_confirm'  => $channel['is_auto_confirm'] ?? false,
                    'is_default'       => $channel['is_default'] ?? false,
                    'flows_id'         => $flow->id,
                ];
            }

            $flow->channels()->createMany($channels);

            DB::commit();
            return Redirect::route(self::ROUTE, [$flow->uuid])
                ->with('toaster', [
                        'message' => trans('modals.flow_created', ['flow' => $request['name']])
                    ]
                );
        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::route('admin.flows.index')
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }

    }

    public function update(string $uuid, Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->updateFlowValidator->validate($request);

        try {
            DB::beginTransaction();
            $flow = Flow::where('uuid', $uuid)->first();
            $flow->update([
                'name'      => $request['name'],
                'objective' => $request['objective']
            ]);

            foreach ($request['channels'] as $channel) {

                Channel::updateOrCreate(
                    [
                        'organisations_id' => auth()->user()->organisations_id,
                        'flows_id'         => $flow->id,
                        'channel_types_id' => $this->getChannelType($channel['channelTypeUuid']),
                    ],
                    [
                        'max_participants' => $channel['max_participants'] ?? 0,
                        'is_auto_confirm'  => $channel['is_auto_confirm'] ?? false,
                        'is_default'       => $channel['is_default'] ?? false,
                    ]
                );
            }

            Channel::where('max_participants', 0)
                ->where('flows_id', $flow->id)
                ->get()
                ->each(function ($channel) {
                    $channel->places()->detach();
                    $channel->channelUsers()->detach();

                    Encounter::where('channels_id', $channel->id)
                        ->where('organisations_id', auth()->user()->organisations_id)
                        ->update(
                            [
                                'channels_id' => null
                            ]
                        );

                    Encounter::whereNull('channels_id')
                        ->where('organisations_id', auth()->user()->organisations_id)
                        ->where('scheduled_at', '>=', now())
                        ->where('tsm_current_state', Encounter::STATE_SCHEDULED)
                        ->get()
                        ->each(function ($encounter) {
                            $encounter->transit('encounter_auto_re_schedule');
                        });

                    $channel->delete();
                });
            DB::commit();
            return Redirect::route(self::ROUTE, [$uuid])
                ->with('toaster', ['message' => trans('modals.flow_edited', ['flow' => $request['name']])]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route(self::ROUTE, [$uuid])
                ->with('toaster', ['message' => getErrorMessage($e), 'type' => 'error']);
        }

    }

    public function getChannelType(string $uuid): int
    {
        return ChannelType::where('uuid', $uuid)->first()->id;
    }
}
