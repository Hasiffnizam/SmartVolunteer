<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\RoleTask;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventJoinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Event $event,
        public RoleTask $roleTask,
    ) {
    }

    public function build()
    {
        return $this
            ->subject('Event Joined: ' . $this->event->title)
            ->view('emails.event_joined');
    }
}
