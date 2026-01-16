<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyEventController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));
    $filter = $request->query('filter', 'upcoming'); // upcoming|ongoing|completed|all

    $today = Carbon::today();

    $eventIds = EventRegistration::query()
      ->where('volunteer_id', $request->user()->id)
      ->pluck('event_id');

    $eventsQuery = Event::query()
      ->whereIn('id', $eventIds)
      ->when($q !== '', function ($query) use ($q) {
        $query->where(function ($sub) use ($q) {
          $sub->where('title', 'like', "%{$q}%")
              ->orWhere('location', 'like', "%{$q}%");
        });
      })
      ->when($filter !== 'all', function ($query) use ($filter, $today) {
        if ($filter === 'upcoming') {
          $query->whereDate('event_date', '>', $today);
        } elseif ($filter === 'ongoing') {
          $query->whereDate('event_date', '=', $today);
        } elseif ($filter === 'completed') {
          $query->whereDate('event_date', '<', $today);
        }
      })
      ->orderBy('event_date', 'asc');

    $events = $eventsQuery->paginate(10)->withQueryString();

    return view('volunteer.myevents.index', compact('events', 'q', 'filter'));
  }

  public function show(Request $request, Event $event)
  {
    // Security: only allow viewing if volunteer actually joined
    $joined = EventRegistration::query()
      ->where('event_id', $event->id)
      ->where('volunteer_id', $request->user()->id)
      ->exists();

    if (!$joined) abort(403);

    $event->load(['roleTasks', 'registrations.volunteer', 'registrations.roleTask']);

    // Find volunteer's chosen role task
    $myRegistration = EventRegistration::query()
      ->where('event_id', $event->id)
      ->where('volunteer_id', $request->user()->id)
      ->with('roleTask')
      ->first();

    return view('volunteer.myevents.show', compact('event', 'myRegistration'));
  }
}
