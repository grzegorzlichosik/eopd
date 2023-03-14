<?php

namespace App\Services\OAuth;

use App\Http\Controllers\OAuthController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\TransferStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OAuthConnectionException;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Nylas\Exceptions\UnauthorizedException;

class OAuthService
{
    public Client $client;

    const GUZZLE_TIMEOUT = 30;

    public string $baseUrl = '/';

    public function __construct()
    {
        $config = [
            'verify' => false,
        ];

        if (env('APP_GUZZLE_DEBUG', false)) {
            $config = array_merge($config, [
                    'debug'    => true,
                    'on_stats' => function (TransferStats $stats) {
                        Log::info(json_encode($stats->getHandlerStats()));
                    }
                ]
            );
        }
        $this->client = new Client($config);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getResponse(
        string $url = '',
        string $method = 'get',
        array  $params = [],
        array  $headers = []
    ): object
    {
        /**
         * Set default headers
         */
        $headers['User-Agent'] = 'TF4CG OAuth app';
        $headers['Accept'] = 'application/json';

        $args = [
            'timeout' => self::GUZZLE_TIMEOUT,
        ];

        if (!empty($params)) {
            $argKey = $this->getParamType($method, $params);
            if (!empty($params['param_type'])) {
                unset($params['param_type']);
            }
            $args[$argKey] = $params;
            if ($argKey === 'json') {
                $headers['Content-Type'] = 'application/json';
            }
        }

        $args['headers'] = $headers;

        try {
            $response = $this->client->request(strtoupper($method), $this->baseUrl . $url, $args);
            $response = json_decode((string)$response->getBody());
            return is_array($response) ? (object)$response : $response;
        } catch (ClientException $e) {
            $message = (env('APP_ENV') === 'production')
                ? trans('errors.client')
                : $e->getMessage();

            throw new OAuthConnectionException($message);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAuthResponse(
        string  $url = '',
        string  $method = 'get',
        array   $params = [],
        ?string $accessToken = ''
    ): object
    {
        try {
            if (is_null($accessToken) || $accessToken === '') {
                throw new OAuthConnectionException('Missing access token');
            }

            $headers['Authorization'] = 'Bearer ' . $accessToken;
            return $this->getResponse($url, $method, $params, $headers);
        } catch (\Exception $e) {

            $message = (env('APP_ENV') === 'production')
                ? trans('errors.auth_token')
                : $e->getMessage();

            throw new OAuthConnectionException($message);
        }
    }

    private function getParamType(string $method = 'get', array $params = []): string
    {
        if ($method === 'get') {
            return 'query';
        }

        if (!empty($params['param_type']) && $params['param_type'] === 'form_params') {
            return 'form_params';
        }

        return 'json';
    }
}
