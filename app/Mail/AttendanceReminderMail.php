<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AttendanceReminderMail extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(
    public Event $event,
    public EventRegistration $registration
  ) {}

  public function build()
  {
    $confirmUrl = URL::temporarySignedRoute(
      'attendance.email.confirm',
      now()->addHours(3),
      ['event' => $this->event->id, 'registration' => $this->registration->id]
    );

    return $this->subject('Reminder: ' . $this->event->title . ' (Check-in link)')
      ->view('emails.attendance_reminder', [
        'event' => $this->event,
        'confirmUrl' => $confirmUrl,
      ]);
  }
}
