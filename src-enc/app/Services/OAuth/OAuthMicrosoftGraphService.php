<?php

namespace App\Services\OAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OAuthConnectionException;
use App\Models\User;

class OAuthMicrosoftGraphService extends OAuthMicrosoftService
{
    public string $baseUrl = 'https://graph.microsoft.com/v1.0/';
}
