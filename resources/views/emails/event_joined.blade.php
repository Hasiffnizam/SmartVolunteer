<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Event Joined</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
  <div style="max-width:640px;margin:0 auto;padding:24px;">

    <div style="border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
      <div style="font-size:18px;font-weight:700;color:#0f172a;">SmartVolunteer</div>

      <h1 style="margin:18px 0 8px 0;font-size:20px;color:#0f172a;">Hi {{ $user->name }}, you're in ✅</h1>

      <p style="margin:0 0 14px 0;color:#334155;line-height:1.6;">
        This email confirms that you have successfully joined the following event.
      </p>

      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;">
        <div style="font-weight:700;color:#0f172a;font-size:16px;">{{ $event->title }}</div>
        <div style="margin-top:10px;color:#334155;line-height:1.7;font-size:14px;">
          <div><strong>Date:</strong> {{ optional($event->event_date)->format('d M Y') }}</div>
          <div><strong>Time:</strong> {{ method_exists($event, 'timeSlotLabel') ? $event->timeSlotLabel() : ucfirst((string) $event->time_slot) }}</div>
          <div><strong>Location:</strong> {{ $event->location }}</div>
          <div><strong>Role:</strong> {{ $roleTask->title ?? 'Volunteer' }}</div>
          @if(!empty($roleTask->description))
            <div><strong>Details:</strong> {{ $roleTask->description }}</div>
          @endif
        </div>
      </div>

      <p style="margin:16px 0 0 0;color:#334155;line-height:1.6;">
        Please make sure to attend on time. If you can’t attend, kindly inform the organizer/admin as early as possible.
      </p>

      <div style="margin-top:18px;">
        <a href="{{ url('/volunteer/my-events') }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:600;">
          View My Events
        </a>
      </div>

      <div style="margin-top:22px;border-top:1px solid #e5e7eb;padding-top:14px;color:#94a3b8;font-size:12px;">
        © {{ date('Y') }} SmartVolunteer
      </div>
    </div>

  </div>
</body>
</html>
