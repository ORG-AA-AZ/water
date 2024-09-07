<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    protected $twilioClient;

    public function __construct(Client $client)
    {
        $this->twilioClient = $client;
    }

    public function sendVerificationCode(string $mobile, string $code)
    {
        $from = config('services.twilio.from');

        try {
            $this->twilioClient->messages->create($mobile, [
                'from' => $from,
                'body' => "Your verification code is: {$code}",
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to send SMS: ' . $e->getMessage());
        }
    }
}
