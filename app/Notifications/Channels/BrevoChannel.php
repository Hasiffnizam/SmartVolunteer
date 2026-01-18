<?php

namespace App\Notifications\Channels;

class BrevoChannel
{
    public function send($notifiable, $notification): void
    {
        if (method_exists($notification, 'toBrevo')) {
            $notification->toBrevo($notifiable);
        }
    }
}
