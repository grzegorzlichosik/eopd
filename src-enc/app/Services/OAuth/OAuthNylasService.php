<?php

namespace App\Services\OAuth;

use App\Exceptions\OAuthConnectionException;

class OAuthNylasService extends OAuthService
{
    public string $baseUrl = 'https://api.nylas.com';
}
