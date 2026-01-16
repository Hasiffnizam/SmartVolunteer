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
      'attendance' => ['required', 'array'],

      // checkbox in your form
      'attendance.*.present' => ['nullable'],

      // optional future: allow status if you add dropdown later
      'attendance.*.status' => ['nullable', 'in:present,absent,late'],

      // optional task completion (0-100) if you add input later
      'attendance.*.task_completion' => ['nullable', 'integer', 'min:0', 'max:100'],

      'attendance.*.note' => ['nullable', 'string', 'max:255'],
    ]);

    $attendance = $validated['attendance'];

    // Only update registrations for this event (security)
    $regs = EventRegistration::where('event_id', $event->id)->get()->keyBy('id');

    foreach ($attendance as $regId => $row) {
      if (!$regs->has($regId)) continue;

      // Priority:
      // 1) if you later add a dropdown (status) -> use it
      // 2) else use checkbox "present" -> present/absent
      $status = $row['status'] ?? null;

      if (!$status) {
        $isPresent = filter_var($row['present'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $status = $isPresent ? 'present' : 'absent';
      }

      $regs[$regId]->update([
        'attendance_status' => $status,
        'task_completion' => isset($row['task_completion'])
          ? (int) $row['task_completion']
          : ($regs[$regId]->task_completion ?? 0),
        'note' => $row['note'] ?? null,
      ]);
    }

    return back()->with('success', 'Attendance saved successfully.');
  }
}
