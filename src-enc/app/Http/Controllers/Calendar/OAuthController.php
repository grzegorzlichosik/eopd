<?php

namespace App\Http\Controllers\Calendar;

use App\Exceptions\OAuthConnectionException;
use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\PlaceType;
use App\Models\User;
use App\Services\OAuth\OAuthMicrosoftGraphService;
use App\Services\OAuth\OAuthMicrosoftService;
use App\Services\OAuth\OAuthOffice356Service;
use App\Services\OAuth\OAuthNylasService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class OAuthController extends Controller
{
    protected const FAILURE = 'LinkCalendar/Failure';
    protected const SUCCESS = 'LinkCalendar/Success';
    private const MS_TOKEN_ERROR = 'errors.ms_token';

    public function __construct(
        protected OAuthOffice356Service      $service,
        protected OAuthMicrosoftGraphService $graphService,
        protected OAuthNylasService          $nylasService,
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function resourcesCallback(Request $request): \Inertia\Response
    {
        $response = $this->callback($request, 'roomCalendar');
        $message = trans('auth.link_room_calendar');

        return $this->renderCallback($response, $message);
    }

    /**
     * @codeCoverageIgnore
     */
    public function calendarCallback(Request $request): \Inertia\Response
    {
        $response = $this->callback($request, 'myCalendar');
        $message = trans('auth.link_my_calendar');

        return $this->renderCallback($response, $message);
    }

    private function renderCallback(string $response, string $message): \Inertia\Response
    {
        if ($response === "success") {
            return Inertia::render(self::SUCCESS, [
                'message' => $message
            ]);
        }
        return Inertia::render(self::FAILURE, [
            'failed' => $response
        ]);
    }

    /**
     * @throws OAuthConnectionException
     *
     * @codeCoverageIgnore
     */
    public function callback(Request $request, string $linkType): string
    {
        $authUser = auth()->user();
        $request = $request->all();

        $redirectUri = env('APP_URL') .
            (
            $linkType === 'roomCalendar'
                ? '/resources/oauth/callback'
                : '/calendar/oauth/callback'
            );

        try {
            if (empty($request['code'])) {
                throw new OAuthConnectionException(trans('errors.oauth_code'));
            }

            $response = $this->getRefreshTokenResponse($request['code'], $redirectUri);

            if (empty($response->refresh_token)) {
                throw new OAuthConnectionException(trans(self::MS_TOKEN_ERROR));
            }

            /**
             * Cache MS access token
             */
            Cache::put(
                'microsoft_access_token_' . $authUser->id,
                $response->access_token,
                new \DateInterval('PT' . $response->expires_in . 'S')
            );

            $authorisedEmail = $this->getAuthorisedUserEmail($response->access_token);

            if ($linkType === "myCalendar") {
                $scope = 'calendar,calendar.free_busy';
                $authUser->microsoft_refresh_token = $response->refresh_token;
                $authUser->office_365_email_id = $authorisedEmail;
                $authUser->save();
            } else {
                $scope = 'calendar,calendar.free_busy,room_resources.read_only';
                $organisation = Organisation::find(auth()->user()->organisations_id);
                $organisation->microsoft_refresh_token = $response->refresh_token;
                $organisation->linked_by = auth()->user()->id;
                $organisation->linked_by_email = $authorisedEmail;
                $organisation->save();
            }

            $response = $this->getNylasCode($response->refresh_token, $authorisedEmail, $scope, $redirectUri);

            if (empty($response->code)) {
                throw new OAuthConnectionException(trans('errors.nylas_code'));
            }

            if ($entity = $this->getNylasToken($response->code, $linkType)) {
                if ($entity instanceof \App\Models\Organisation) {
                    $this->getResources($entity->nylas_access_token);
                }

            } else {
                throw new OAuthConnectionException(trans('errors.nylas_token'));
            }

            return "success";

        } catch (OAuthConnectionException $e) {
            return (env('APP_ENV') === 'production')
                ? trans('errors.callback')
                : $e->getTraceAsString();
        }
    }

    /**
     * @throws OAuthConnectionException
     *
     * @codeCoverageIgnore
     */
    private function getAuthorisedUserEmail(string $accessToken): string
    {
        try {
            $response = $this->graphService->getAuthResponse('/me', 'get', [], $accessToken);
        } catch (\Exception $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans(self::MS_TOKEN_ERROR)
                : $e->getTraceAsString();

            throw new OAuthConnectionException($message);
        }

        return $response->mail;
    }

    /**
     * @throws OAuthConnectionException
     *
     * @codeCoverageIgnore
     */
    private function getResources(string $accessToken): object
    {
        try {
            $response = $this->nylasService->getAuthResponse('/resources', 'get', [], $accessToken);
        } catch (\Exception $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans(self::MS_TOKEN_ERROR)
                : $e->getTraceAsString();

            throw new OAuthConnectionException($message);
        }

        foreach ((array)$response as $resource) {
            Place::updateOrCreate(
                [
                    'email'            => $resource->email,
                    'organisations_id' => auth()->user()->organisations_id,
                ],
                [
                    'name'           => $resource->name,
                    'place_types_id' => PlaceType::RESOURCED,
                    'external_id'    => '',
                    'metadata'       => json_encode(
                        [
                            'capacity'     => $resource->capacity,
                            'building'     => $resource->building,
                            'floor_name'   => $resource->floor_name,
                            'floor_number' => $resource->floor_number,
                        ]
                    )
                ]
            );
        }

        return $response;
    }

    /**
     * @throws OAuthConnectionException
     *
     * @codeCoverageIgnore
     */
    private function getRefreshTokenResponse(string $code, string $redirectUri): ?object
    {
        $params = [
            'client_id'     => env('MS_CLIENT_ID'),
            'client_secret' => env('MS_CLIENT_SECRET'),
            'redirect_uri'  => $redirectUri,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'param_type'    => 'form_params',
            'scope'         => OAuthMicrosoftService::SCOPE,
        ];

        try {
            return $this->service->getResponse('/token', 'post', $params);
        } catch (\Exception $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans(self::MS_TOKEN_ERROR)
                : $e->getTraceAsString();

            throw new OAuthConnectionException($message);
        }
    }

    /**
     * @throws OAuthConnectionException
     *
     * @codeCoverageIgnore
     */
    private function getNylasCode(
        string $refreshToken,
        string $authorisedEmail,
        string $scope,
        string $redirectUri
    ): ?object
    {
        $authUser = Auth::user();
        $params =
            [
                'client_id'     => env('NYLAS_CLIENT_ID', ''),
                'response_type' => 'code',
                'name'          => $authUser->name,
                'email_address' => $authorisedEmail,
                'provider'      => 'office365',
                'scopes'        => $scope,
                'redirect_uri'  => env('APP_URL') . 'calendar/link/token',
                'settings'      => [
                    'microsoft_client_id'     => env('MS_CLIENT_ID'),
                    'microsoft_client_secret' => env('MS_CLIENT_SECRET'),
                    'microsoft_refresh_token' => $refreshToken,
                    'redirect_uri'            => $redirectUri,
                ]
            ];

        try {
            $response = $this->nylasService->getResponse('/connect/authorize', 'post', $params);
        } catch (\Exception $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans('errors.nylas_code')
                : $e->getTraceAsString();

            throw new OAuthConnectionException($message);
        }
        return $response;

    }

    /**
     * @throws OAuthConnectionException
     *
     * @codeCoverageIgnore
     */
    private function getNylasToken(string $code, string $linkType): User|Organisation
    {
        $entity = auth()->user();
        if ($linkType === 'roomCalendar') {
            $entity = Organisation::find(auth()->user()->organisations_id);
        }

        $params = [
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'client_id'     => env('NYLAS_CLIENT_ID', ''),
            'client_secret' => env('NYLAS_CLIENT_SECRET', ''),
        ];

        try {
            $response = $this->nylasService->getResponse('/connect/token', 'post', $params);
        } catch (\Exception $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans('errors.nylas_token')
                : $e->getTraceAsString();

            throw new OAuthConnectionException($message);
        }

        foreach (['access_token', 'account_id', 'provider'] as $key) {
            if (isset($response->$key)) {
                $property = 'nylas_' . $key;
                $entity->$property = $response->$key;
            }
        }

        $entity->save();

        return $entity->refresh();
    }
}
