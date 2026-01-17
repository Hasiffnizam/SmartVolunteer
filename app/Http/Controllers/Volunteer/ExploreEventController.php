<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RoleTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventJoinedMail;

class ExploreEventController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));

    $events = Event::query()
      ->withCount('registrations')
      ->when($q !== '', function ($query) use ($q) {
        $query->where(function ($sub) use ($q) {
          $sub->where('title', 'like', "%{$q}%")
              ->orWhere('location', 'like', "%{$q}%");
        });
      })
      ->orderBy('event_date', 'asc')
      ->paginate(9)
      ->withQueryString();

    return view('volunteer.explore.index', compact('events', 'q'));
  }

  public function show(Event $event, Request $request)
  {
    $event->load([
      'roleTasks' => function ($q) {
        $q->orderBy('id', 'asc');
      }
    ]);

    $alreadyJoined = EventRegistration::query()
      ->where('event_id', $event->id)
      ->where('volunteer_id', $request->user()->id)
      ->exists();

    return view('volunteer.explore.show', compact('event', 'alreadyJoined'));
  }

  public function join(Request $request, Event $event)
  {
    $validated = $request->validate([
      'role_task_id' => ['required', 'integer', 'exists:role_tasks,id'],
    ]);

    $volunteerId = $request->user()->id;

    // Transaction + lock prevents 2 volunteers taking last slot at same time
    $result = DB::transaction(function () use ($event, $validated, $volunteerId) {

      // Prevent joining same event twice
      $already = EventRegistration::query()
        ->where('event_id', $event->id)
        ->where('volunteer_id', $volunteerId)
        ->exists();

      if ($already) {
        return ['ok' => false, 'msg' => 'You already joined this event.'];
      }

      // Lock role task row
      $roleTask = RoleTask::query()
        ->where('id', $validated['role_task_id'])
        ->where('event_id', $event->id) // IMPORTANT: ensure role task belongs to this event
        ->lockForUpdate()
        ->first();

      if (!$roleTask) {
        return ['ok' => false, 'msg' => 'Invalid role task selection.'];
      }

      $total = (int) ($roleTask->slots ?? 0);
      $taken = (int) ($roleTask->slots_taken ?? 0);

      if ($total <= 0) {
        return ['ok' => false, 'msg' => 'This role task has no slots configured yet.'];
      }

      if ($taken >= $total) {
        return ['ok' => false, 'msg' => 'This role task is already full.'];
      }

      // Create registration
      EventRegistration::create([
        'event_id' => $event->id,
        'role_task_id' => $roleTask->id,
        'volunteer_id' => $volunteerId,
        'joined_at' => now(),
      ]);

      // Increase taken slots
      $roleTask->increment('slots_taken');

      return ['ok' => true, 'msg' => 'You successfully joined the event!', 'role_task_id' => $roleTask->id];
    });

    

    // Send confirmation email (only if join was successful)
    if (($result['ok'] ?? false) === true) {
      try {
        $roleTask = RoleTask::find($result['role_task_id']);
        if ($roleTask) {
          Mail::to($request->user()->email)->send(new EventJoinedMail($request->user(), $event, $roleTask));
        }
      } catch (\Throwable $e) {
        // Do not block joining if mail fails
      }
    }

return back()->with($result['ok'] ? 'success' : 'error', $result['msg']);
  }
}
