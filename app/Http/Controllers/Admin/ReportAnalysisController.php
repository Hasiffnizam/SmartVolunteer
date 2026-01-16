<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::query()
            ->with([
                'cause',
                'registrations',
                'registrations.volunteer',
            ])
            ->latest('event_date')
            ->get();

        $totalEvents = $events->count();
        $completedEvents = $events->where('status', 'Completed')->count();

        $allRegs = $events->flatMap->registrations;

        $totalAssignments = $allRegs->count();
        $totalPresent = $allRegs->where('attendance_status', 'present')->count();

        $overallAttendanceRate = $totalAssignments > 0
            ? round(($totalPresent / $totalAssignments) * 100)
            : 0;

        $overallTaskCompletion = $totalAssignments > 0
            ? round($allRegs->avg(fn ($r) => (int) ($r->task_completion ?? 0)))
            : 0;

        $eventsWithRates = $events->map(function ($e) {
            $total = $e->registrations->count();
            $present = $e->registrations->where('attendance_status', 'present')->count();

            $rate = $total > 0 ? round(($present / $total) * 100) : null;

            $completion = $total > 0
                ? round($e->registrations->avg(fn ($r) => (int) ($r->task_completion ?? 0)))
                : null;

            return [
                'event' => $e,
                'total' => $total,
                'attendance_rate' => $rate,
                'completion_avg' => $completion,
            ];
        })->filter(fn ($x) => $x['total'] > 0)->values();

        $bestEvents = $eventsWithRates->sortByDesc('attendance_rate')->take(5)->values();
        $worstEvents = $eventsWithRates->sortBy('attendance_rate')->take(5)->values();

        $popularCauses = $events
            ->groupBy('cause_id')
            ->map(function ($group) {
                $causeName = optional($group->first()->cause)->name ?? 'Unknown';
                return [
                    'cause' => $causeName,
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->take(6);

        $monthlyTrends = $events->groupBy(function ($e) {
            return $e->event_date
                ? Carbon::parse($e->event_date)->format('Y-m')
                : 'unknown';
        })->map(function ($group, $ym) {
            $regs = $group->flatMap->registrations;

            return [
                'month' => $ym,
                'assignments' => $regs->count(),
                'unique_volunteers' => $regs->pluck('volunteer_id')->unique()->count(),
            ];
        })->sortBy('month')->values()->take(-8);

        $insights = [];

        if ($totalEvents === 0) {
            $insights[] = ['type' => 'warn', 'text' => 'No events found. Create events to generate analytics.'];
        } else {
            if ($totalAssignments === 0) {
                $insights[] = ['type' => 'warn', 'text' => 'No registrations found across all events. Encourage volunteers to join events.'];
            } else {
                if ($overallAttendanceRate < 70) {
                    $insights[] = ['type' => 'warn', 'text' => "Overall attendance is low ({$overallAttendanceRate}%). Consider stronger reminders and clearer check-in flow."];
                } elseif ($overallAttendanceRate >= 85) {
                    $insights[] = ['type' => 'good', 'text' => "Overall attendance is strong ({$overallAttendanceRate}%). Volunteer engagement looks healthy."];
                }

                if ($overallTaskCompletion < 70) {
                    $insights[] = ['type' => 'warn', 'text' => "Overall task completion is {$overallTaskCompletion}%. Consider clearer task briefing and supervision."];
                } elseif ($overallTaskCompletion >= 85) {
                    $insights[] = ['type' => 'good', 'text' => "High task completion ({$overallTaskCompletion}%) indicates effective coordination."];
                }
            }

            if ($completedEvents / max(1, $totalEvents) < 0.4) {
                $insights[] = ['type' => 'info', 'text' => 'Many events are still ongoing/upcoming. Analytics becomes more meaningful as events complete.'];
            }
        }

        return view('admin.report_analysis.index', compact(
            'totalEvents',
            'completedEvents',
            'overallAttendanceRate',
            'overallTaskCompletion',
            'bestEvents',
            'worstEvents',
            'popularCauses',
            'monthlyTrends',
            'insights'
        ));
    }
}
