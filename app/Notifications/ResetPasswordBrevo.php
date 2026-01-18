<?php

namespace App\Notifications;

use App\Notifications\Channels\BrevoChannel;
use App\Services\BrevoMailer;
use Illuminate\Notifications\Notification;

class ResetPasswordBrevo extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        // ✅ No database, no mail — only our custom Brevo channel
        return [BrevoChannel::class];
    }

    public function toBrevo($notifiable): void
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        // Use blade if you created it; otherwise use inline HTML.
        $html = "
            <h2>Password Reset Request</h2>
            <p>Hi ".e($notifiable->name ?? 'Volunteer').",</p>
            <p>Click the link below to reset your password:</p>
            <p><a href='{$url}'>{$url}</a></p>
            <p>If you did not request this, you can ignore this email.</p>
        ";

        // ✅ Send via Brevo API (cURL version)
        BrevoMailer::send(
            $notifiable->email,
            $notifiable->name ?? 'Volunteer',
            'Reset your SmartVolunteer password',
            $html
        );
    }
}
