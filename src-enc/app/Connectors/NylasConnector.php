<?php
namespace App\Connectors;

use Nylas\Client;

class NylasConnector
{
    public Client $client;

    public function __construct()
    {
        $config =
            [
                'client_id'     => env('NYLAS_CLIENT_ID', ''),              // required
                'client_secret' => env('NYLAS_CLIENT_SECRET', ''),          // required
                'debug'         => env('NYLAS_CLIENT_DEBUG', false),
                'region'        => env('NYLAS_CLIENT_REGION', 'us'),        // server region us, ireland or canada
                'log_file'      => storage_path('logs/nylas.log'),                // a file path or a resource handler
            ];

        $this->client = new Client($config);
    }
}
