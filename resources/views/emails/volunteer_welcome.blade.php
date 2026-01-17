<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome to SmartVolunteer</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
  <div style="max-width:640px;margin:0 auto;padding:24px;">

    <div style="border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
      <div style="font-size:18px;font-weight:700;color:#0f172a;">SmartVolunteer</div>

      <h1 style="margin:18px 0 8px 0;font-size:20px;color:#0f172a;">Hi {{ $user->name }}, welcome aboard 🎉</h1>

      <p style="margin:0 0 12px 0;color:#334155;line-height:1.6;">
        Your volunteer account has been created successfully.
      </p>

      <p style="margin:0 0 16px 0;color:#334155;line-height:1.6;">
        Next step: log in and explore events that match your interests.
      </p>

      <p style="margin:0 0 6px 0;color:#64748b;font-size:13px;">
        If you did not register for SmartVolunteer, you can ignore this email.
      </p>

      <div style="margin-top:18px;">
        <a href="{{ url('/login') }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:600;">
          Login to SmartVolunteer
        </a>
      </div>

      <div style="margin-top:22px;border-top:1px solid #e5e7eb;padding-top:14px;color:#94a3b8;font-size:12px;">
        © {{ date('Y') }} SmartVolunteer
      </div>
    </div>

  </div>
</body>
</html>
