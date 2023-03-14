<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Validators\AddUserValidator;


class PoolUserController extends Controller
{

    private const SHOW_PAGE_ROUTE_NAME = 'admin.pools.show';

    public function __construct(
        private readonly AddUserValidator $addUserValidator
    )
    {
    }

    public function store(Request $request, string $uuid): RedirectResponse
    {
        $request = $request->all();
        $this->addUserValidator->validate($request);
        try {
            $pool = $this->getPool($uuid);

            $selectedUsersUuids = collect($request['selected_users'])
                ->pluck('uuid')
                ->toArray();

            User::where('organisations_id', auth()->user()->organisations_id)
                ->whereUuidIn($selectedUsersUuids)
                ->get()
                ->each(function ($user) use ($pool) {
                    $pool->users()->attach($user->id);
                });

            return redirect(route(self::SHOW_PAGE_ROUTE_NAME, ['uuid' => $pool->uuid]), 303)
                ->with('toaster', [
                        'message' => trans(
                            'modals.pool_users', ['pool' => $pool->name]
                        )
                    ]
                );
        } catch (\Throwable $e) {
            return redirect(route(self::SHOW_PAGE_ROUTE_NAME, ['uuid' => $pool->uuid]), 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type' => 'error'
                    ]
                );
        }
    }

    public function destroy(string $uuid, string $userUuid): RedirectResponse
    {
        try {
            $pool = $this->getPool($uuid);

            $user = User::where('organisations_id', auth()->user()->organisations_id)
                ->whereUuid($userUuid)
                ->firstOrFail();

            $pool->users()->detach($user->id);

            return redirect(route(self::SHOW_PAGE_ROUTE_NAME, ['uuid' => $pool->uuid]), 303)
                ->with('toaster', [
                        'message' => trans(
                            'modals.pool_user_deleted',
                            ['pool' => $pool->name, 'user' => $user->name])
                    ]
                );
        } catch (\Throwable $e) {
            return redirect(route(self::SHOW_PAGE_ROUTE_NAME, ['uuid' => $pool->uuid]), 303)
                ->with('toaster', [
                        'message'=> getErrorMessage($e),
                        'type' => 'error'
                    ]
                );
        }
    }

    public function search(Request $request, string $uuid): JsonResponse
    {
        $request = $request->all();
        try {
            $pool = $this->getPool($uuid);

            $result = User::where('organisations_id', auth()->user()->organisations_id)
                ->where('name', 'LIKE', '%' . $request['search'] . '%');
            if (isset($request['selected'])) {
                $result = $result->whereNotIn('uuid', $request['selected']);
            }
            $result = $result->whereDoesntHave('pools', function (Builder $query) use ($pool) {
                $query->where('pools_id', $pool->id);
            })
                ->get(['uuid', 'name']);
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(getErrorMessage($e));
        }
    }

    private function getPool(string $uuid): Pool
    {
        return Pool::where('organisations_id', auth()->user()->organisations_id)
            ->whereUuid($uuid)
            ->firstOrFail();
    }

}
