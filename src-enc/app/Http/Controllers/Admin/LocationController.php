<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Validators\NewLocationValidator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Validators\UpdateLocationValidator;
use App\Validators\UploadFileValidator;

class LocationController extends Controller
{
    private const ROUTE = 'admin.resources.locations.index';

    public function __construct(
        private readonly NewLocationValidator    $newLocationValidator,
        private readonly UpdateLocationValidator $updateLocationValidator,
        private readonly UploadFileValidator     $uploadFileValidator
    )
    {
    }

    public function index(): \Inertia\Response
    {
        list($sortField, $sortOrder) = getTableSorting();

        $locations = Location::where('locations.organisations_id', auth()->user()->organisations_id)
            ->with('file:id,uuid')
            ->withCount('places')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    return $q->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('timezone', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(20)
            ->appends(cleanupAppends(request()->input()));

        return Inertia::render('Admin/Locations', [
            'locations'          => $locations,
            'preferredCountries' => explode("|", env('PREFERRED_COUNTRIES', "US|IE|GB")),
            'timezones'          => \DateTimeZone::listIdentifiers(\DateTimeZone::ALL),
            'searchValue'        => request()->input('search')
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->newLocationValidator->validate($request);

        try {
            DB::beginTransaction();
            $location = Location::create(
                [
                    'organisations_id' => auth()->user()->organisations_id,
                    'name'             => $request['name'],
                    'short_name'       => $request['short_name'],
                    'address_1'        => $request['address'],
                    'postcode'         => $request['postcode'],
                    'city_town'        => $request['city_town'],
                    'location_lat'     => $request['location_lat'],
                    'location_lon'     => $request['location_lon'],
                    'timezone'         => $request['timezone'],
                    'phone'            => $request['phone'],
                ]
            );

            if (!empty($request['file'])) {
                $fileId = $this->uploadInstructions($request['file']);
                $location->files_id = $fileId;
                $location->save();
            }

            DB::commit();
            return Redirect::route(self::ROUTE)
                ->with('toaster', [
                        'message' => trans('modals.location_added', ['location' => $request['name']])
                    ]
                );
        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::route(self::ROUTE)
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

        auth()->user()->country_code = $request['country_code'];

        $this->updateLocationValidator->validate($request);

        try {
            Location::whereUuid($uuid)
                ->update(
                    [
                        'name'         => $request['name'],
                        'short_name'   => $request['short_name'],
                        'address_1'    => $request['address'],
                        'postcode'     => $request['postcode'],
                        'city_town'    => $request['city_town'],
                        'location_lat' => $request['location_lat'],
                        'location_lon' => $request['location_lon'],
                        'timezone'     => $request['timezone'],
                        'phone'        => $request['phone']
                    ]
                );
            return Redirect::route(self::ROUTE)
                ->with('toaster', [
                        'message' => trans('modals.location_edited', ['location' => $request['name']])
                    ]
                );
        } catch (\Throwable $e) {
            return Redirect::route(self::ROUTE)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    public function updateFile(Request $request, string $uuid): RedirectResponse
    {
        $this->uploadFileValidator->validate($request->all());
        $fileId = $this->uploadInstructions($request['file']);
        Location::where('uuid', $uuid)
            ->update([
                'files_id' => $fileId
            ]);
        return Redirect::route(self::ROUTE)
            ->with('toaster', [
                    'message' => trans('modals.location_file_edited', ['location' => $request['name']]
                    )
                ]
            );

    }

    public function uploadInstructions(UploadedFile $fileUpload): int
    {
        $fileName = $fileUpload->getClientOriginalName();
        $fileSize = $fileUpload->getSize();
        $fileMimeType = $fileUpload->getClientMimeType();

        $file = File::create([
            'organisations_id' => auth()->user()->organisations_id,
            'users_id'         => auth()->user()->id,
            'name'             => $fileName,
            'mimetype'         => $fileMimeType,
            'size'             => $fileSize
        ]);

        Storage::putFileAs(
            auth()->user()->organisation->uuid . '/locations',
            $fileUpload,
            $file->uuid->toString() . '.' . $fileUpload->getClientOriginalExtension()
        );

        return $file->id;

    }

    public function downloadInstructions(string $uuid): JsonResponse|StreamedResponse
    {
        $file = File::where('uuid', $uuid)
            ->with('organisation')
            ->first();

        $path = auth()->user()->organisation->uuid . '/locations/' . $file->uuid . '.pdf';

        if (Storage::exists($path)) {
            return Storage::download($path, $file->name);
        }

        return response()->json(trans('errors.file_does_not_exist'));
    }

}

