<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Organisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Validators\UpdateOrganisationValidator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrganisationController extends Controller
{
    private const INDEX = 'profile.show';
    private const HEIGHT = 100;
    private const WIDTH = 150;

    public function __construct(
        private readonly UpdateOrganisationValidator $updateOrganisationValidator
    )
    {
    }

    public function update(Request $request): RedirectResponse
    {
        $request = $request->all();

        auth()->user()->country_code = $request['country_code'];
        $this->updateOrganisationValidator->validate($request);
        $organisation = auth()->user()->organisation;

        try {
            DB::beginTransaction();

            $organisation->name = $request['name'];
            $organisation->phone_number = $request['phone_number'];
            $organisation->header_mail_color = $request['color'];

            if (!empty($request['file'])) {
                $image = file_get_contents($request['file']);

                $file = File::create([
                    'organisations_id' => $organisation->id,
                    'users_id'         => auth()->id(),
                    'name'             => $request['file_name'] . '.' . $request['file_type'],
                    'mimetype'         => $request['file_mimetype'],
                    'size'             => strlen($image)
                ]);
                $filename = $file->uuid->toString() . '.' . $request['file_type'];
                $path = auth()->user()->organisation->uuid . '/logo/';


                Storage::put(
                    $path . $filename,
                    $image
                );

                $resizeFileName = $file->
                    uuid->
                    toString() . '_250x100.' . $request['file_type'];
                $thumb = Image::make($image);
                $thumbWidth = $thumb->width();
                $thumbHeight = $thumb->height();
                $newWidth = $thumbWidth <= $thumbHeight ? self::WIDTH : null;
                $newHeight = $thumbWidth > $thumbHeight ? self::HEIGHT : null;

                $thumb->resize($newWidth, $newHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });

                Storage::put($path . $resizeFileName, $thumb->encode());

                $organisation->files_id = $file->id;

            }
            $organisation->save();

            DB::commit();
            return Redirect::route(Self::INDEX)
                ->with(
                    'toaster',
                    [
                        'message' => trans('modals.organisation_updated')
                    ]
                );
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route(Self::INDEX)
                ->with(
                    'toaster',
                    [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    public function show(): ?Organisation
    {
        $organisation = auth()->user()->organisation;
        $organisation->hasLogo = $organisation->files_id;

        return $organisation;
    }

    public function showImage(?string $size = null): ?StreamedResponse
    {
        $path = auth()->user()->organisation->uuid . '/logo/';
        $organisation = Organisation::find(auth()
            ->user()
            ->organisations_id
        );
        $file = File::find($organisation->files_id);

        if ($file) {
            $filename = $file->uuid . ($size ? '_' . $size : '') . '.' . explode('.', $file->name)[1];
            if (Storage::exists($path . $filename)) {
                return Storage::response($path . $filename, $file->name);
            }
            return null;
        }
        return null;
    }

    public function destroy(): RedirectResponse
    {
        try {
            $organisation = Organisation::findOrFail(auth()->user()->organisations_id);
            $file = File::findOrFail($organisation->files_id);

            DB::beginTransaction();
            Organisation::where('id', auth()->user()->organisation->id)
                ->update([
                    'files_id' => null
                ]);
            $file->deleteOrFail();
            DB::commit();

            return Redirect::route(Self::INDEX)
                ->with(
                    'toaster',
                    [
                        'message' => trans('modals.logo_deleted')
                    ]
                );
        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::route(Self::INDEX)
                ->with(
                    'toaster',
                    [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }
}
