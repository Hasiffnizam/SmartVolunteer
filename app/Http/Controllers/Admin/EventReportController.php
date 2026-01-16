<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function show(Request $request, Event $event)
    {
        // ✅ Use registrations, not event_volunteers pivot
        $event->load([
            'cause',
            'skills',
            'roleTasks',
            'registrations.volunteer',
            'registrations.roleTask',
        ]);

        $registrations = $event->registrations;

        // ---------------------------
        // TOP KPI CARDS
        // ---------------------------
        $totalVolunteers = $registrations->pluck('volunteer_id')->unique()->count();

        $totalAssignments = $registrations->count();
        $presentCount = $registrations->where('attendance_status', 'present')->count();

        $attendanceRate = $totalAssignments > 0
            ? round(($presentCount / $totalAssignments) * 100)
            : 0;

        $avgCompletion = $totalAssignments > 0
            ? round($registrations->avg(fn ($r) => (int) ($r->task_completion ?? 0)))
            : 0;

        $eventStatus = $event->status ?? 'Unknown';

        // ---------------------------
        // MIDDLE ANALYTICS
        // ---------------------------

        // Attendance analysis by role
        $attendanceByRole = $registrations->groupBy('role_task_id')->map(function ($rows, $roleTaskId) {
            $assigned = $rows->count();
            $present  = $rows->where('attendance_status', 'present')->count();
            $late     = $rows->where('attendance_status', 'late')->count();
            $absent   = $rows->where('attendance_status', 'absent')->count();

            $roleName = optional($rows->first()->roleTask)->title ?? 'Unknown Role';

            return [
                'role_task_id' => $roleTaskId,
                'role' => $roleName,
                'assigned' => $assigned,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'attendance_rate' => $assigned > 0 ? round(($present / $assigned) * 100) : 0,
            ];
        })->values();

        // ✅ Blade expects $byRole
        $byRole = $attendanceByRole;

        // Role & task performance (Required vs Assigned vs Present + completion)
        $rolePerformance = $event->roleTasks->map(function ($task) use ($registrations) {
            $rows = $registrations->where('role_task_id', $task->id);

            $assigned = $rows->count();
            $present  = $rows->where('attendance_status', 'present')->count();

            $avg = $assigned > 0
                ? round($rows->avg(fn ($r) => (int) ($r->task_completion ?? 0)))
                : 0;

            $required = (int) ($task->slots ?? 0);

            return [
                'role_task_id' => $task->id,
                'role' => $task->title,
                'required_slots' => $required,
                'assigned' => $assigned,
                'present' => $present, // ✅ Blade uses this
                'completion_avg' => $avg,
                'is_underfilled' => $required > 0 && $assigned < $required,
            ];
        });

        // ---------------------------
        // Skill utilization (REAL, safe with users.skills column existing)
        // IMPORTANT: do NOT use $u->skills->contains() (skills column is string)
        // Use relation query exists() instead.
        // ---------------------------

        $uniqueVolunteers = $registrations
            ->map(fn ($r) => $r->volunteer)
            ->filter()
            ->unique('id')
            ->values();

        $skillUtilization = $event->skills->map(function ($skill) use ($uniqueVolunteers, $totalVolunteers) {

            $count = $uniqueVolunteers->filter(function ($u) use ($skill) {
                return $u->skills()->where('skills.id', $skill->id)->exists();
            })->count();

            $percent = $totalVolunteers > 0 ? round(($count / $totalVolunteers) * 100) : 0;

            return [
                'skill' => $skill->name,
                'count' => $count,
                'percent' => $percent,
            ];
        });

        // ---------------------------
        // Auto insights
        // ---------------------------
        $insights = [];

        if ($totalAssignments === 0) {
            $insights[] = ['type' => 'warn', 'text' => 'No volunteers registered for this event yet.'];
        } else {
            if ($attendanceRate < 70) {
                $insights[] = ['type' => 'warn', 'text' => "Attendance is low ({$attendanceRate}%). Consider stronger reminders or clearer check-in process."];
            } elseif ($attendanceRate >= 85) {
                $insights[] = ['type' => 'good', 'text' => "Strong attendance rate ({$attendanceRate}%). Volunteer engagement looks healthy."];
            }

            if ($avgCompletion < 70) {
                $insights[] = ['type' => 'warn', 'text' => "Average task completion is {$avgCompletion}%. Consider improving task briefing and supervision."];
            } elseif ($avgCompletion >= 85) {
                $insights[] = ['type' => 'good', 'text' => "High task completion ({$avgCompletion}%) indicates effective coordination."];
            }

            $underfilledCount = $rolePerformance->where('is_underfilled', true)->count();
            if ($underfilledCount > 0) {
                $insights[] = ['type' => 'warn', 'text' => "{$underfilledCount} role(s) are underfilled. Consider recruiting more volunteers or adjusting slots."];
            }
        }

        // Volunteer rows (optional table later)
        $volunteerRows = $registrations->map(function ($r) {
            return [
                'name' => optional($r->volunteer)->name ?? 'Unknown',
                'email' => optional($r->volunteer)->email ?? null,
                'role' => optional($r->roleTask)->title ?? 'Unknown Role',
                'attendance_status' => $r->attendance_status ?? 'absent',
                'task_completion' => (int) ($r->task_completion ?? 0),
                'note' => $r->note ?? null,
            ];
        });

        return view('admin.events.report', compact(
            'event',
            'totalVolunteers',
            'attendanceRate',
            'avgCompletion',
            'eventStatus',
            'byRole',
            'rolePerformance',
            'skillUtilization',
            'insights',
            'volunteerRows'
        ));
    }
}
