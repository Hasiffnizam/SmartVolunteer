<!doctype html>
<html>
<body style="font-family: Arial, sans-serif; line-height: 1.5;">
  <h2>Reminder: {{ $event->title }}</h2>
  <p>
    Your event starts in about <strong>1 hour</strong>.
    <br>
    Location: <strong>{{ $event->location }}</strong>
    <br>
    Date: <strong>{{ $event->event_date?->format('d M Y') }}</strong>
    <br>
    Slot: <strong>{{ ucfirst($event->time_slot) }}</strong>
  </p>

  <p style="margin: 20px 0;">
    <a href="{{ $confirmUrl }}"
       style="display:inline-block;background:#f97316;color:#fff;padding:12px 18px;border-radius:10px;text-decoration:none;font-weight:bold;">
      Mark Attendance
    </a>
  </p>

  <p style="color:#666;font-size:12px;">
    This link expires and is unique to you.
  </p>
</body>
</html>
