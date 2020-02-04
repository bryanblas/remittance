<?php

namespace App\Repositories\Passport;

use Exception;
use GuzzleHttp\Client as GuzzleClient;

class PassportRepository implements PassportInterface
{
    const CREATE_TOKEN_URL = '/oauth/token';

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new GuzzleClient([
            'headers' => [
                'Accept' => 'application/json'
             ]
        ]);
    }

    public function getToken($request)
    {
        try {
            $response =$this->httpClient->post(env('APP_URL') . self::CREATE_TOKEN_URL, [
                'form_params' => [
                    'client_id' => env('CLIENT_ID'),
                    'client_secret' => env('CLIENT_SECRET'),
                    'grant_type' => 'password',
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' =>  ''//implode(' ', $permissions)
                ]
            ]);
            return $response;
        } catch (Exception $e) {
            return false;
        }
    }
}
