<?php

namespace App\Services\OAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OAuthConnectionException;
use App\Models\User;

class OAuthMicrosoftService extends OAuthService
{
    public string $baseUrl = 'https://login.windows.net/common/oauth2/v2.0';
    const SCOPE = [
        'offline_access',
        'OnlineMeetings.ReadWrite',
        'openid',
        'Place.Read.All',
        'profile',
        'User.Read',
        'EAS.AccessAsUser.All',
        'EWS.AccessAsUser.All'
    ];

    public function getAuthUrl(User $user, string $redirectUri): string
    {
        $params = [
            'client_id'     => env('MS_CLIENT_ID'),
            'redirect_uri'  => $redirectUri,
            'login_hint'    => $user->email,
            'response_type' => 'code',
            'scope'         => implode(' ', self::SCOPE),
        ];
        $query = http_build_query($params, '', '&');

        return $this->baseUrl . '/authorize?' . $query;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAccessToken(User $user, bool $withScopes = false): ?string
    {
        $accessToken = Cache::get('microsoft_access_token_' . $user->id);

        if (!$accessToken) {
            $response = $this->refreshAccessToken($user, $withScopes);
            $accessToken = $response->access_token;
            Cache::put(
                'microsoft_access_token_' . $user->id,
                $accessToken,
                new \DateInterval('PT' . $response->expires_in . 'S')
            );
        }

        return $accessToken;
    }

    /**
     * @codeCoverageIgnore
     */
    private function refreshAccessToken(User $user, $withScopes): ?object
    {
        $params = [
            'client_id'     => env('MS_CLIENT_ID'),
            'client_secret' => env('MS_CLIENT_SECRET'),
            'refresh_token' => $user?->microsoft_refresh_token,
            'grant_type'    => 'refresh_token',
            'param_type'    => 'form_params',
        ];

        if ($withScopes) {
            $params['scope'] = implode(' ', self::SCOPE);
        }

        return $this->getResponse('/token', 'post', $params);
    }
}
