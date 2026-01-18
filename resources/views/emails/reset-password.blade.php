<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
  <h2>Password Reset Request</h2>

  <p>Hi {{ $user->name ?? 'Volunteer' }},</p>

  <p>
    We received a request to reset your SmartVolunteer password.
    Click the button below to set a new password:
  </p>

  <p style="margin: 20px 0;">
    <a href="{{ $url }}"
       style="display:inline-block;padding:12px 18px;text-decoration:none;border-radius:8px;background:#111827;color:#fff;">
      Reset Password
    </a>
  </p>

  <p>If the button doesn’t work, copy & paste this link:</p>
  <p><a href="{{ $url }}">{{ $url }}</a></p>

  <p style="margin-top: 24px; color:#6b7280;">
    If you did not request this, you can ignore this email.
  </p>

  <p>— SmartVolunteer</p>
</body>
</html>
