<?php

namespace App\Services;

use Google\Client;
use Google\Service\Directory;
use Google\Service\Directory\User;
use Google\Service\Directory\UserName;
use GuzzleHttp\Psr7\Response;


class GoogleWorkspaceService
{
    public Directory $directory;

    public function __construct()
    {
        $config = json_decode(env('GOOGLE_APPLICATION_CREDENTIALS', '[]'), true);

        $client = new Client();
        if ($config) {
            $client->setAuthConfig($config);
        }
        $client->addScope(Directory::ADMIN_DIRECTORY_USER);
        $client->addScope(Directory::ADMIN_DIRECTORY_USER_SECURITY);
        $client->setSubject('gsuite@whencounter.us');

        $this->directory = new Directory($client);
    }

    /**
     * @codeCoverageIgnore
     */
    public function createUser(array $user): User
    {
        $googleUserName = new UserName();
        $googleUserName->familyName = $user['familyName'];
        $googleUserName->givenName = $user['givenName'];

        $googleUser = new User();
        $googleUser->password = $user['password'];
        $googleUser->primaryEmail = $user['email'];
        $googleUser->setName($googleUserName);

        return $this->directory->users->insert($googleUser);
    }

    /**
     * @codeCoverageIgnore
     */
    public function deleteUser(string $userKey): Response
    {
        return $this->directory->users->delete($userKey);
    }

}
