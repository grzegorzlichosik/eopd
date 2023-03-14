<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Validators\CreatePoolValidator;
use App\Validators\UpdatePoolValidator;


class PoolController extends Controller
{
    private const ROUTE = 'admin.pools.show';
    private const CREATE = 'modals.pool_added';
    private const UPDATE = 'modals.pool_edited';
    private const INDEX = 'admin.pools';

    public function __construct(
        private readonly CreatePoolValidator $createPoolValidator,
        private readonly UpdatePoolValidator $updatePoolValidator,
    )
    {
    }

    public function index(): \Inertia\Response
    {
        list($sortField, $sortOrder) = getTableSorting();

        $pools = Pool::where('organisations_id', auth()->user()->organisations_id)
            ->orderBy($sortField, $sortOrder)
            ->paginate(20)
            ->appends(cleanupAppends(request()->input()));

        return Inertia::render('Admin/Pools', [
            'pools' => $pools,
        ]);
    }

    public function show(string $uuid): \Inertia\Response
    {
        list($sortField, $sortOrder) = getTableSorting();

        $pool = $this->getPool($uuid);
        $users = $pool->users()
            ->orderBy($sortField, $sortOrder)
            ->paginate(20);

        $usersData = collect($users->items())->map(function ($user) {
            return [
                'uuid'  => $user->uuid,
                'name'  => $user->name,
                'email' => $user->email,
            ];
        });

        $users->setCollection($usersData);
        $pool->setRelation('users', $users);

        return Inertia::render('Admin/Pool', [
            'pool' => $pool
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->createPoolValidator->validate($request);

        Pool::create([
            'name'             => $request['name'],
            'organisations_id' => auth()->user()->organisations_id,
            'created_at'       => now(),
        ]);

        return Redirect::route('admin.pools')
            ->with('toaster', [
                    'message' => trans(
                        self::CREATE, ['pool' => $request['name']])
                ]
            );
    }

    public function update(Request $request, string $uuid): RedirectResponse
    {
        $request = $request->all();
        $this->updatePoolValidator->validate($request);

        try {
            $pool = $this->getPool($uuid);
            $pool->name = $request['name'];
            $pool->save();

            return redirect(route(self::ROUTE, ['uuid' => $pool->uuid]), 303)
                ->with('toaster',
                    [
                        'message' => trans(
                            self::UPDATE,
                            [
                                'pool' => $request['name']
                            ]
                        )
                    ]
                );
        } catch (ModelNotFoundException $e) {
            return redirect(route(self::INDEX), 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        } catch (\Throwable $e) {
            return redirect(route(self::ROUTE, ['uuid' => $pool->uuid]), 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    private function getPool(string $uuid): Pool
    {
        return Pool::where('organisations_id', auth()->user()->organisations_id)
            ->whereUuid($uuid)
            ->firstOrFail();
    }

}
