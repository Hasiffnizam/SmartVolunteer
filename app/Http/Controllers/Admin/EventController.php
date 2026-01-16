<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Skill;
use App\Models\Cause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString(); // upcoming|ongoing|completed|all

        $events = Event::query()
            ->with(['cause'])
            ->withCount([
                'roleTasks as tasks_count',
                'skills as skills_count',
                'registrations as registrations_count',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, ['upcoming','ongoing','completed']), function ($query) use ($status) {
                $today = Carbon::today();

                if ($status === 'upcoming') {
                    $query->whereDate('event_date', '>', $today);
                } elseif ($status === 'ongoing') {
                    $query->whereDate('event_date', '=', $today);
                } elseif ($status === 'completed') {
                    $query->whereDate('event_date', '<', $today);
                }
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.EventManage', compact('events', 'q'));
    }

    public function create()
    {
        $skills = Skill::orderBy('name')->get();
        $causes = Cause::orderBy('name')->get();

        return view('admin.events.create', compact('skills', 'causes'));
    }

    public function show(Event $event)
    {
        $event->load(['skills', 'cause'])
            ->loadCount([
                'roleTasks as tasks_count',
                'skills as skills_count',
                'registrations as registrations_count',
            ]);

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        // ✅ Edit page is event info only (tasks edited in role-task page)
        $event->load(['cause', 'skills']);

        $skills = Skill::orderBy('name')->get();
        $causes = Cause::orderBy('name')->get();

        return view('admin.events.edit', compact('event', 'skills', 'causes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'event_date' => ['required', 'date'],
            'time_slot' => ['required', 'in:morning,evening,night'],
            'location' => ['required', 'string', 'max:255'],

            'cause_id' => ['required', 'integer', 'exists:causes,id'],

            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],

            // ✅ Create MUST include tasks
            'role_tasks_json' => ['required', 'string'],
        ]);

        // Parse tasks JSON (frontend sends: [{name, slots}, ...])
        $tasks = [];
        $decoded = json_decode($validated['role_tasks_json'], true);

        if (is_array($decoded)) {
            foreach ($decoded as $t) {
                $name = trim((string)($t['name'] ?? ''));
                $slots = (int)($t['slots'] ?? $t['slots_total'] ?? 0);

                if ($name !== '' && $slots > 0) {
                    $tasks[] = [
                        'title' => $name,
                        'slots' => $slots,
                        'slots_taken' => 0,
                    ];
                }
            }
        }

        if (count($tasks) === 0) {
            return back()
                ->withErrors(['role_tasks_json' => 'Please add at least 1 role task with slots.'])
                ->withInput();
        }

        // Poster upload
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('event-posters', 'public');
        }

        DB::transaction(function () use ($validated, $posterPath, $tasks) {

            $event = Event::create([
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_date' => $validated['event_date'],
                'time_slot' => $validated['time_slot'],
                'location' => $validated['location'],
                'cause_id' => $validated['cause_id'],
                'poster_path' => $posterPath,
                'status' => 'Upcoming',
            ]);

            // ✅ save into role_tasks
            $event->roleTasks()->createMany($tasks);

            // ✅ Sync required skills for this event
            $event->skills()->sync($validated['skill_ids'] ?? []);
        });

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully!');
    }

    /**
     * ✅ UPDATE EVENT INFO ONLY (NO ROLE TASKS HERE)
     * Role Tasks are edited in updateRoleTask()
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'event_date' => ['required', 'date'],
            'time_slot' => ['required', 'in:morning,evening,night'],
            'location' => ['required', 'string', 'max:255'],

            'cause_id' => ['required', 'integer', 'exists:causes,id'],
            'status' => ['required', 'in:Upcoming,Ongoing,Completed'],

            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        DB::transaction(function () use ($request, $event, $validated) {

            // Poster upload (optional replace)
            if ($request->hasFile('poster')) {
                if ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) {
                    Storage::disk('public')->delete($event->poster_path);
                }
                $event->poster_path = $request->file('poster')->store('event-posters', 'public');
            }

            // ✅ Update event fields only
            $event->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_date' => $validated['event_date'],
                'time_slot' => $validated['time_slot'],
                'location' => $validated['location'],
                'cause_id' => $validated['cause_id'],
                'status' => $validated['status'],
                'poster_path' => $event->poster_path,
            ]);

            // ✅ Sync skills
            $event->skills()->sync($validated['skill_ids'] ?? []);
        });

        return redirect()
            ->route('admin.events.show', $event->id)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        if ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) {
            Storage::disk('public')->delete($event->poster_path);
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted.');
    }

    // ✅ Role Task page
    public function roleTask(Event $event)
    {
        $event->load('roleTasks');

        return view('admin.events.role-task', compact('event'));
    }

    // ✅ Save role tasks from role_tasks_json into role_tasks table
    public function updateRoleTask(Request $request, Event $event)
    {
        $validated = $request->validate([
            'role_tasks_json' => ['required', 'string'],
        ]);

        $decoded = json_decode($validated['role_tasks_json'], true);

        if (!is_array($decoded)) {
            return back()
                ->withErrors(['role_tasks_json' => 'Invalid tasks format.'])
                ->withInput();
        }

        $incoming = [];
        foreach ($decoded as $t) {
            $id = isset($t['id']) ? (int)$t['id'] : null;
            $name = trim((string)($t['name'] ?? ''));
            $slots = (int)($t['slots'] ?? $t['slots_total'] ?? 0);

            if ($name === '') continue;

            if ($slots < 1) {
                return back()
                    ->withErrors(['role_tasks_json' => "Slots must be at least 1 for task: {$name}"])
                    ->withInput();
            }

            $incoming[] = [
                'id' => $id ?: null,
                'title' => $name,
                'slots' => $slots,
            ];
        }

        if (count($incoming) === 0) {
            return back()
                ->withErrors(['role_tasks_json' => 'Please add at least 1 role task with slots.'])
                ->withInput();
        }

        DB::transaction(function () use ($event, $incoming) {

            $existing = $event->roleTasks()->get()->keyBy('id');
            $existingIds = $existing->keys()->all();

            $incomingIds = collect($incoming)
                ->pluck('id')
                ->filter()
                ->map(fn ($v) => (int)$v)
                ->values()
                ->all();

            // delete removed tasks
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                $event->roleTasks()->whereIn('id', $toDelete)->delete();
            }

            foreach ($incoming as $t) {
                if (!empty($t['id']) && isset($existing[$t['id']])) {
                    // update title + slots only (keep slots_taken)
                    $existing[$t['id']]->update([
                        'title' => $t['title'],
                        'slots' => (int)$t['slots'],
                    ]);
                } else {
                    $event->roleTasks()->create([
                        'title' => $t['title'],
                        'slots' => (int)$t['slots'],
                        'slots_taken' => 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.events.role-task', $event->id)
            ->with('success', 'Role tasks saved successfully!');
    }
}
