<?php

namespace App\Http\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class TurboSmsService
{
    protected array $phones;
    protected string $endpoint;
    protected string $message;
    protected string $token;
    protected Client $client;

    public function __construct(array $phones)
    {
        $this->phones = $phones;
        $this->endpoint = 'https://api.turbosms.ua/message/send.json';
        $this->client = new Client();
        $this->token = 'febca7e697f702cf52973bbaa937e6b43e845ff5';
//        $this->token = 'f804200249701809163d85edc4c77ac29139fd44';


    }

    public function sendCode()
    {
        try {
            $payload = $this->preparePayload();
            $code = $payload['form_params']['sms']['text'];


            $result = $this->client->post($this->endpoint, $this->preparePayload());

            return ['sendResult' => $result->getBody()->getContents(),
                'code' => $code
            ];

        } catch (Exception $exception) {
            Log::error($exception);
        }
    }

    public function generateCode(): int
    {
        return rand(1000, 9999);
    }

    private function preparePayload()
    {
        return [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $this->token
            ],
            'form_params' => [
                'sender' => 'turboSms',
                'recipients' => $this->phones,
                'sms' => ['sender' => 'PHPUkraine', 'text' => strval($this->generateCode())]
            ]
        ];
    }
}
