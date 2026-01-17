<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\URL;

class EventCheckInController extends Controller
{
  public function show(Event $event)
  {
    // Signed URL for volunteers; requires login (volunteer)
    $signedUrl = URL::temporarySignedRoute(
      'checkin.qr.show',
      now()->addMinutes(30),
      ['event' => $event->id]
    );

    return view('admin.events.checkin', [
      'event' => $event,
      'signedUrl' => $signedUrl,
    ]);
  }
}
