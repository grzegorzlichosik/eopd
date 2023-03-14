<?php

namespace App\Http\Controllers\Platform;

use App\Models\Organisation;
use App\Http\Controllers\Controller;
use App\Services\GoogleWorkspaceService;
use App\Services\NylasService;
use App\Services\OAuth\OAuthNylasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Google\Service\Exception as Google_Service_Exception;

class OrganisationController extends Controller
{
    private const INDEX = 'platform.organisations.index';

    public function __construct(
        public GoogleWorkspaceService $googleWorkspaceService,
        public NylasService           $nylasService
    )
    {
    }

    public function index(): Response
    {
        $organisations = Organisation::whereNull('is_platform')
            ->with(
                [
                    'created_by'
                ]
            )
            ->withCount('users')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    return $q->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('phone_number', 'like', '%' . $search . '%');
                });
            });
        if (request()->input('sortField')) {
            $sortOrder = "ASC";
            if (request()->input('sortOrder') === '-1') {
                $sortOrder = "DESC";
            }
            $organisations = $organisations->orderBy(request()->input('sortField'), $sortOrder);
        } else {
            $organisations = $organisations->orderBy('name', "ASC");
        }

        $organisations = $organisations->paginate(20)
            ->appends(cleanupAppends(request()->input()));

        return Inertia::render('Platform/Organisations', [
            'organisations' => $organisations,
            'searchValue'   => request()->input('search'),
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function createMasterCalendar(string $uuid, Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $organisation = $this->getOrganisation($uuid);

            $organisation->master_calendar_email =
                env('APP_ENV', 'local') . '_' .
                $organisation->uuid . '@' . env('MASTER_CALENDAR_DOMAIN', 'whencounter.us');

            $organisation->master_calendar_password = generatePassword();

            $createdUser = $this->googleWorkspaceService->createUser(
                [
                    'familyName' => 'Master Calendar',
                    'givenName'  => $organisation->name,
                    'email'      => $organisation->master_calendar_email,
                    'password'   => $organisation->master_calendar_password,
                ]
            );

            $organisation->master_calendar_userkey = $createdUser['id'];

            $organisation->save();
            DB::commit();
            return to_route(self::INDEX, [], 303)
                ->with('toaster', [
                        'message' => 'created'
                    ]
                );

        } catch (Google_Service_Exception $e) {
            $errors = collect($e->getErrors())->first();
            $message = $errors['reason'] === 'duplicate'
                ? trans('errors.google_account_exists')
                : $errors['message'];
            DB::rollBack();
            return redirect(route(self::INDEX), 303)
                ->with('toaster', [
                        'message' => $message,
                        'type'    => 'error'
                    ]
                );

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect(route(self::INDEX), 303)
                ->with('message', getErrorMessage($e));
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function deleteMasterCalendar(string $uuid, Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $organisation = $this->getOrganisation($uuid);

            $this->googleWorkspaceService->deleteUser($organisation->master_calendar_userkey);

            $organisation->master_calendar_email = null;
            $organisation->master_calendar_password = null;
            $organisation->master_calendar_userkey = null;

            if ($organisation->master_calendar_nylas_account_id) {
                $this->nylasService->deleteUser($organisation->master_calendar_nylas_account_id);

                $organisation->master_calendar_nylas_account_id = null;
                $organisation->master_calendar_nylas_access_token = null;
                $organisation->master_calendar_nylas_primary_calendar_id = null;
            }
            $organisation->save();
            DB::commit();

            return to_route(self::INDEX, [], 303)
                ->with('message', 'Master Calendar has been disconnected');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect(route(self::INDEX), 303)
                ->with('toaster', [
                        'message' => getErrorMessage($e),
                        'type'    => 'error'
                    ]
                );
        }
    }

    public function showMasterCalendarPassword(string $uuid): \Illuminate\Http\JsonResponse
    {
        $organisation = $this->getOrganisation($uuid);

        if ($organisation) {
            return response()->json([
                'password' => $organisation->master_calendar_password,
                'email'    => $organisation->master_calendar_email,
            ]);
        }

        return response()->json('');
    }

    private function getOrganisation(string $uuid): ?Organisation
    {
        return Organisation::whereUuid($uuid)->first();
    }
}
