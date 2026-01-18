<?php

namespace App\Notifications;

use App\Services\BrevoMailer;
use Illuminate\Notifications\Notification;

class ResetPasswordBrevo extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        // Dummy channel, we send manually
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        $html = view('emails.reset-password', [
            'user' => $notifiable,
            'url' => $url,
        ])->render();

        BrevoMailer::send(
            $notifiable->email,
            $notifiable->name ?? 'Volunteer',
            'Reset your SmartVolunteer password',
            $html
        );

        return ['sent_via' => 'brevo'];
    }
}
