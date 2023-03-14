<?php

namespace App\Services;

use App\Connectors\NylasConnector;
use App\Models\Organisation;
use App\Models\User;
use App\Services\OAuth\OAuthOffice356Service;
use Illuminate\Support\Facades\Session;
use Nylas\Client;
use Illuminate\Support\Facades\Auth;
use App\Services\OAuth\OAuthNylasService;

class NylasService
{
    protected Client $nylasClient;

    protected OAuthNylasService $oAuthService;
    protected OAuthOffice356Service $oAuthOffice356Service;

    public function __construct(
        NylasConnector        $nylasConnector,
        OAuthNylasService     $oAuthService,
        OAuthOffice356Service $oAuthOffice356Service,
    )
    {
        $this->nylasClient = $nylasConnector->client;
        $this->oAuthService = $oAuthService;
        $this->oAuthOffice356Service = $oAuthOffice356Service;
    }

    public function authNative(string $redirectUri): ?string
    {
        return $this->oAuthOffice356Service->getAuthUrl(auth()->user(), $redirectUri);
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function authHosted(string $redirectUrl): ?string
    {
        $organisation = Organisation::where('uuid', Session::get('organisationUuid'))->first();

        $params =
            [
                'state'         => 'testing',
                'login_hint'    => $organisation->master_calendar_email,
                'response_type' => 'code',
                'scopes'        => 'calendar,calendar.free_busy,room_resources.read_only',
                'redirect_uri'  => $redirectUrl
            ];

        return $this->nylasClient->Authentication->Hosted->authenticateUser($params);
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function getHostedAuthToken(string $code): object
    {

        $params = [
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'client_id'     => env('NYLAS_CLIENT_ID', ''),
            'client_secret' => env('NYLAS_CLIENT_SECRET', ''),
        ];
        try {
            $response = $this->oAuthService->getResponse('/oauth/token', 'post', $params);
        } catch (\Exception $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans('errors.master_callback')
                : $e->getTraceAsString();

            throw new \Google_Service_Exception($message);
        }
        return $response;


    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function deleteUser($userId): array
    {
        return $this->nylasClient->Management->Account->deleteAnAccount($userId);
    }

}
