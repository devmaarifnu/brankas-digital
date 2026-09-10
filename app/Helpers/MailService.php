<?php
namespace App\Helpers;

use GuzzleHttp\Client;

class MailService {

    public static function send(array $data)
    {
        $client = new Client();

        $response = $client->post(env("MAIL_SERIVICE_URI"), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        // Get the response body as a string
        $body = $response->getBody()->getContents();

        return $body;
    }

}
