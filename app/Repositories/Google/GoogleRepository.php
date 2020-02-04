<?php

namespace App\Repositories\Google;

use GuzzleHttp\Client as GuzzleClient;

class GoogleRepository implements GoogleInterface
{
    private $httpClient;

    const RECAPTCHA_URL = '/recaptcha/api/siteverify';

    public function __construct()
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => env('GOOGLE_URL')
        ]);
    }

    public function validateReCaptcha($value)
    {
        $response = $this->httpClient->request('POST', self::RECAPTCHA_URL, [
            'form_params' =>
                [
                    'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
                    'response' => $value
                ]
        ]);
        $body = json_decode((string)$response->getBody());
        return $body->success;
    }
}
