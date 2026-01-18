<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    public static function send(string $toEmail, string $toName, string $subject, string $html): void
    {
        $apiKey = env('BREVO_API_KEY');

        if (!$apiKey) {
            Log::error('Brevo API key missing (BREVO_API_KEY).');
            return;
        }

        $payload = [
            'sender' => [
                'name'  => env('MAIL_FROM_NAME', 'SmartVolunteer'),
                'email' => env('MAIL_FROM_ADDRESS', 'no-reply@smartvolunteer.app'),
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName],
            ],
            'subject' => $subject,
            'htmlContent' => $html,
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                'api-key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 15,
        ]);

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Brevo success is usually 201
        if ($body === false || $err || $code < 200 || $code >= 300) {
            Log::error('Brevo email failed', [
                'http_code' => $code,
                'curl_error' => $err,
                'response_body' => $body,
                'to' => $toEmail,
                'subject' => $subject,
            ]);
        }
    }
}
