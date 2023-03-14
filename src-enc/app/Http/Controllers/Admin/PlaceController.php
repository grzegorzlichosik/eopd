<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Place;
use App\Models\PlaceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Validators\PlaceValidator;
use App\Validators\NewPlaceValidator;

class PlaceController extends Controller
{
    private const ROUTE = 'admin.resources.places.index';

    public function __construct(
        private readonly PlaceValidator    $validator,
        private readonly NewPlaceValidator $newPlaceValidator
    )
    {
    }

    public function index(): \Inertia\Response
    {
        list($sortField, $sortOrder) = getTableSorting();

        if ($sortField === 'location_uuid') {
            $sortField = 'location_name';
        }

        $places = Place::where('places.organisations_id', auth()->user()->organisations_id)
            ->withAggregate('location', 'uuid')
            ->withAggregate('location', 'name')
            ->withAggregate('place_type', 'label')
            ->withAggregate('place_type', 'name')
            ->withAggregate('place_type', 'uuid')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    return $q->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('tsm_current_state', 'like', $search . '%')
                        ->orWhereHas('place_type', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('location', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(20)
            ->appends(cleanupAppends(request()->input()));

        return Inertia::render('Admin/Places', [
            'places'      => $places,
            'locations'   => convertToDropdownData(Location::where('organisations_id', auth()->user()->organisations_id)
                ->get()),
            'placeTypes'  => convertToDropdownData(PlaceType::where('id', '!=', PlaceType::RESOURCED)
                ->orderBy('name')
                ->get()),
            'placeStatus' => [
                ['name' => trans('status.active'), 'value' => 'Active'],
                ['name' => trans('status.in_active'), 'value' => 'Inactive']
            ],
            'searchValue' => request()->input('search')
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->newPlaceValidator->validate($request);

        try {
            DB::beginTransaction();
            $place = Place::create(
                [
                    'organisations_id' => Auth::user()->organisations_id,
                    'locations_id'     => Location::whereUuid($request['location_uuid'])
                        ->where('organisations_id', auth()->user()->organisations_id)
                        ->first()?->id,
                    'place_types_id'   => PlaceType::whereUuid($request['place_type_uuid'])->first()?->id,
                    'name'             => $request['name'],
                    'description'      => $request['description'],
                ]
            );

            $place->transit('place_create');

            if ($request['status'] === Place::STATE_ACTIVE) {
                $place->transit('place_activate');
            }

            DB::commit();
            return Redirect::route(self::ROUTE, [], 303)
                ->with(
                    'toaster',
                    [
                        'message' => trans('modals.place_created', ['place' => $request['name']])
                    ]
                );
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route(self::ROUTE, [], 303)
                ->with(
                    'toaster',
                    [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    public function update(string $uuid, Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->validator->validate($request);
        try {
            $place = $this->getPlace($uuid);

            if (!empty($request['location_uuid'])) {
                $request['locations_id'] = Location::whereUuid($request['location_uuid'])
                    ->where('organisations_id', auth()->user()->organisations_id)
                    ->first()?->id;
            }

            if (!empty($request['place_type_uuid'])) {
                $request['place_types_id'] = PlaceType::whereUuid($request['place_type_uuid'])
                    ->first()?->id;
            }

            $place->fill($request);
            $place->save();


            if (!empty($request['status']) && $request['status'] !== $place->tsm_current_state) {
                $transition = $request['status'] === Place::STATE_ACTIVE
                    ? 'place_activate'
                    : 'place_deactivate';

                $place->transit($transition);
            }

            return Redirect::route(self::ROUTE, [], 303)
                ->with(
                    'toaster',
                    [
                        'message' => trans('modals.place_updated', ['place' => $request['name']])
                    ]
                );
        } catch (\Throwable $e) {
            return Redirect::route(self::ROUTE, [], 303)
                ->with(
                    'toaster',
                    [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    public function destroy(string $uuid): RedirectResponse
    {
        try {
            $place = $this->getPlace($uuid);
            $place->transit('place_archive');

            return Redirect::route(self::ROUTE, [], 303)
                ->with('toaster', [
                        'message' => trans('modals.place_updated', ['place' => $place->name])
                    ]
                );
        } catch (\Throwable $e) {
            return Redirect::route(self::ROUTE, [], 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    private function getPlace(string $uuid): ?Place
    {
        return Place::whereUuid($uuid)
            ->where('organisations_id', auth()->user()->organisations_id)
            ->firstOrFail();
    }
}

