<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Validators\AddAgentValidator;

class FlowAgentController extends Controller
{
    private const ROUTE = 'admin.flows.agents';

    public function __construct(
        private readonly AddAgentValidator $addAgentValidator
    )
    {
    }

    public function store(string $uuid, Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->addAgentValidator->validate($request);

        try {
            $flow = $this->getFlow($uuid);

            $selectedAgentsUuids = collect($request['selected_agents'])
                ->pluck('poolUuid', 'uuid')
                ->toArray();

            $poolsByUuid = Pool::whereUuidIn($selectedAgentsUuids)
                ->where('organisations_id', auth()->user()->organisations_id)
                ->pluck('id', 'uuid');

            $users = User::whereUuidIn(array_keys($selectedAgentsUuids))
                ->where('organisations_id', auth()->user()->organisations_id)
                ->get();

            foreach ($selectedAgentsUuids as $userUuid => $poolUuid) {
                $user = $users->where('uuid', $userUuid)->first();
                if ($user) {
                    $flow->users()->attach($user->id, ['pools_id' => $poolsByUuid[$poolUuid]]);
                }
            }

            return Redirect::route(self::ROUTE, [$uuid])
                ->with('toaster', ['message' => trans('modals.flow_agents', ['flow' => $flow->name])]);
        } catch (\Exception $e) {
            return Redirect::route(self::ROUTE, [$uuid])
                ->with('toaster', ['message' => getErrorMessage($e), 'type' => 'error']);
        }
    }

    public function update(string $uuid, Request $request): RedirectResponse
    {
        $request = $request->all();
        try {
            $flow = $this->getFlow($uuid);
            $agent = $this->getUser($request['email']);
            $action = $request['checkedStatus'] ? 'create' : 'delete';
            $channel = Channel::where('flows_id', $flow->id)
                ->with('type')
                ->whereHas('type', function ($query) use ($request) {
                    $query->where('label', $request['type']);
                })
                ->firstorFail();

            if ($action === 'create') {
                $flow->channelUsers()->attach($agent->id, ['channels_id' => $channel->id]);
            } else {
                $agent->channels()->detach($channel->id);
            }

            return Redirect::route(self::ROUTE, [$uuid])
                ->with('toaster', ['message' => trans('modals.agent_channel', ['agent' => $agent->name])]);
        } catch (\Throwable $e) {
            return Redirect::route(self::ROUTE, [$uuid])
                ->with('toaster', ['message' => getErrorMessage($e), 'type' => 'error']);
        }
    }

    public function searchAgent(string $uuid, Request $request): JsonResponse
    {
        $request = $request->all();

        try {
            $flow = $this->getFlow($uuid);

            $result = User::where('organisations_id', auth()->user()->organisations_id)
                ->where('is_agent', 1)
                ->whereNotNull('nylas_account_id')
                ->where('name', 'LIKE', '%' . $request['search'] . '%')
                ->with('pools')
                ->whereDoesntHave('flows', function (Builder $query) use ($flow) {
                    $query->where('flows_id', $flow->id);
                });

            if (isset($request['selected'])) {
                $result = $result->whereNotIn('uuid', $request['selected']);
            }

            $agents = [];
            $result->get()
                ->each(function ($user) use (&$agents) {
                    $user?->pools?->each(function ($pool) use (&$agents, $user) {
                        $agents[] = [
                            "uuid"     => $user->uuid,
                            "poolUuid" => $pool->uuid,
                            "name"     => $user->name . ' (' . $pool->name . ')'
                        ];
                    });
                });
        } catch (\Throwable $e) {
            $agents = [];
        }

        return response()->json($agents);
    }

    public function destroy(string $flowUuuid, string $uuid): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $flow = $this->getFlow($flowUuuid);
            $agent = User::whereUuid($uuid)->first();
            $flow->users()->detach($agent->id);
            $flow->channelUsers()->detach($agent->id);

            Encounter::where('flows_id', $flow->id)
                ->where('agent_id', $agent->id)
                ->where('scheduled_at', '>=', now())
                ->get()
                ->each(function ($encounter) {
                    $encounter->agent_id = null;
                    $encounter->save();
                    if ($encounter->tsm_current_state === Encounter::STATE_SCHEDULED) {
                        $encounter->transit('encounter_auto_re_schedule');
                    }

                });

            DB::commit();
            return Redirect::route(self::ROUTE, [$flowUuuid])
                ->with('toaster', ['message' => trans('modals.agent_deleted', ['agent' => $agent->name])]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route(self::ROUTE, [$flowUuuid])
                ->with('toaster', ['message' => getErrorMessage($e), 'type' => 'error']);
        }
    }

    public function getFlow(string $uuid): Flow
    {
        return Flow::where('uuid', $uuid)->first();
    }

    public function getUser(string $email): User
    {
        return User::where('organisations_id', auth()->user()->organisations_id)
            ->where('email', $email)
            ->firstOrFail();
    }

}
