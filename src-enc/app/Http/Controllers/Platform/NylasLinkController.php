<?php

namespace App\Http\Controllers\Platform;

use App\Exceptions\OAuthConnectionException;
use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Services\NylasService;
use Google\Service\Exception as Google_Service_Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class NylasLinkController extends Controller
{
    protected const FAILURE = 'Platform/Failure';

    public function __construct(
        protected NylasService $service
    )
    {
    }
    /**
     *
     * @codeCoverageIgnore
     */
    public function masterHosted(string $uuid)
    {
        $redirectUri = env('APP_URL') . '/master/oauth/callback';
        Session::put('organisationUuid', $uuid);
        $url = $this->service->authHosted($redirectUri);
        return $url ? Inertia::location($url) : Redirect::back();
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function getHostedAuthToken(Request $request): \Inertia\Response
    {
        $request = $request->all();
        $organisation = Organisation::where('uuid', Session::get('organisationUuid'))->first();

        if (!empty($request['code'])) {
            try {
                $response = $this->service->getHostedAuthToken($request['code']);
                foreach (['access_token', 'account_id'] as $key) {
                    if (isset($response->$key)) {
                        $property = 'master_calendar_nylas_' . $key;
                        $organisation->$property = $response->$key;
                    }
                }
                $organisation->save();

            } catch (Google_Service_Exception $e) {
                return Inertia::render(self::FAILURE, [
                    'failed' => $e->getTraceAsString()
                ]);
            }

        } else {
            $message = (env('APP_ENV') === 'production')
                ? trans('errors.invalid_code')
                : trans('errors.invalid_response_code');

            return Inertia::render(self::FAILURE, [
                'failed' => $message
            ]);
        }
        Session::forget('organisationUuid');
        return Inertia::render('Platform/Success', [
            'message' => trans('modals.gsuite_nylas_connected', ['organisation' => $organisation->name])
        ]);
    }

    public function retry():  \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $organisation = Organisation::where('uuid', Session::get('organisationUuid'))->first();
        if ($organisation) {
           $organisation->master_calendar_nylas_access_token = null;
           $organisation->master_calendar_nylas_account_id   = null;
           $organisation->save();
        }
        return \redirect('/platform/organisations/'.  Session::get('organisationUuid').'/master_nylas');

    }


}
