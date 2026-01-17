<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show(Event $event)
    {
        $registrations = EventRegistration::with(['volunteer', 'roleTask'])
            ->where('event_id', $event->id)
            ->get();

        $byTask = $registrations->groupBy('role_task_id');

        return view('admin.events.attendance', [
            'event' => $event,
            'registrations' => $registrations,
            'byTask' => $byTask,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'attendance' => ['nullable', 'array'],

            // checkbox in your form
            'attendance.*.present' => ['nullable'],

            // optional future: dropdown
            'attendance.*.status' => ['nullable', 'in:present,absent,late'],

            'attendance.*.task_completion' => ['nullable', 'integer', 'min:0', 'max:100'],
            'attendance.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $attendance = $validated['attendance'] ?? [];

        // All registrations for this event
        $regs = EventRegistration::where('event_id', $event->id)->get()->keyBy('id');

        foreach ($regs as $regId => $reg) {

            // If admin submitted a row for this regId, use it. If not submitted, treat as "not touched".
            $row = $attendance[$regId] ?? null;

            // ✅ Important: If volunteer already checked in (self/email), do NOT overwrite to absent.
            // We consider checked-in as "present" if:
            // - attendance_status is present OR
            // - checked_in_at exists OR
            // - present == 1
            $alreadyPresent = ($reg->attendance_status === 'present')
                || !is_null($reg->checked_in_at)
                || ((int) $reg->present === 1);

            // If admin didn't touch this row, keep existing values
            if ($row === null) {
                continue;
            }

            // Determine status from dropdown or checkbox
            $status = $row['status'] ?? null;

            if (!$status) {
                $isPresent = filter_var($row['present'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $status = $isPresent ? 'present' : 'absent';
            }

            // ✅ If admin tries to set absent but volunteer already present → keep present
            if ($status === 'absent' && $alreadyPresent) {
                $status = 'present';
            }

            $isPresentFinal = ($status === 'present') ? 1 : 0;

            $updateData = [
                'attendance_status' => $status,
                'present' => $isPresentFinal,
                'task_completion' => isset($row['task_completion'])
                    ? (int) $row['task_completion']
                    : ($reg->task_completion ?? 0),
                'note' => $row['note'] ?? null,
            ];

            // If admin marked present and no checked_in_at yet, fill it
            if ($status === 'present' && is_null($reg->checked_in_at)) {
                $updateData['checked_in_at'] = now();
                $updateData['check_in_method'] = 'admin';
            }

            $reg->update($updateData);
        }

        return back()->with('success', 'Attendance saved successfully.');
    }
}
