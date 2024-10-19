<?php

namespace App\Services\Sms;

use Twilio\Rest\Client;

class SmsService implements ServiceTwilioSms
{
    protected $twilioClient;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        if (empty($sid) || empty($token)) {
            throw new \InvalidArgumentException('Twilio credentials are missing.');
        }

        $this->twilioClient = new Client($sid, $token);
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
