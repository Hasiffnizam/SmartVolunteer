<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RoleTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\BrevoMailer;

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

        $result = DB::transaction(function () use ($event, $validated, $volunteerId) {

            $already = EventRegistration::query()
                ->where('event_id', $event->id)
                ->where('volunteer_id', $volunteerId)
                ->exists();

            if ($already) {
                return ['ok' => false, 'msg' => 'You already joined this event.'];
            }

            $roleTask = RoleTask::query()
                ->where('id', $validated['role_task_id'])
                ->where('event_id', $event->id)
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

            EventRegistration::create([
                'event_id' => $event->id,
                'role_task_id' => $roleTask->id,
                'volunteer_id' => $volunteerId,
                'joined_at' => now(),
            ]);

            $roleTask->increment('slots_taken');

            return ['ok' => true, 'msg' => 'You successfully joined the event!', 'role_task_id' => $roleTask->id];
        });

        // Send confirmation email via Brevo API
        if (($result['ok'] ?? false) === true) {
            try {
                $roleTask = RoleTask::find($result['role_task_id']);
                $user = $request->user();

                if ($roleTask) {
                    $html = view('emails.event-joined', [
                        'user' => $user,
                        'event' => $event,
                        'roleTask' => $roleTask,
                    ])->render();

                    BrevoMailer::send(
                        $user->email,
                        $user->name ?? 'Volunteer',
                        'Event Joined: ' . $event->title,
                        $html
                    );

                    Log::info('Join event email sent (Brevo)', [
                        'email' => $user->email,
                        'event_id' => $event->id,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Join event email failed (Brevo)', [
                    'email' => $request->user()->email,
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with($result['ok'] ? 'success' : 'error', $result['msg']);
    }
}
