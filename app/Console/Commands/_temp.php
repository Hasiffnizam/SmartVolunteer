<?php

namespace App\Console\Commands;

use App\Mail\AttendanceReminderMail;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAttendanceReminders extends Command
{
  protected $signature = 'attendance:send-reminders {--window=5 : Minutes window}';
  protected $description = 'Send attendance reminder emails 1 hour before event start';

  private function slotStartTime(string $timeSlot): string
  {
    return match ($timeSlot) {
      'morning' => '09:00:00',
      'evening' => '14:00:00',
      'night'   => '20:00:00',
      default   => '09:00:00',
    };
  }

  public function handle(): int
  {
    $tz = config('app.timezone');
    $window = (int) $this->option('window');

    $now = Carbon::now($tz);
    $target = $now->copy()->addHour();
    $from = $target->copy()->subMinutes($window);
    $to   = $target->copy()->addMinutes($window);

    $events = Event::with(['registrations.volunteer'])->get();
    $sent = 0;
    $matched = 0;

    foreach ($events as $event) {
      if (!$event->event_date) continue;

      $start = Carbon::parse(
        $event->event_date->format('Y-m-d') . ' ' . $this->slotStartTime($event->time_slot),
        $tz
      );

      if (!$start->between($from, $to)) continue;

      $matched++;

      foreach ($event->registrations as $reg) {
        $email = $reg->volunteer?->email;
        if (!$email) continue;

        Mail::to($email)->send(new AttendanceReminderMail($event, $reg));
        $sent++;
      }
    }

    $this->info("Matched events: {$matched}, emails sent: {$sent}");
    return self::SUCCESS;
  }
}
