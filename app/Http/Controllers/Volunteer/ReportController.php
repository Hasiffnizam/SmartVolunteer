<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) abort(403);

        $userId = $user->id;

        $joinTable = 'event_registrations';

        /**
         * Base: all registrations for this volunteer
         */
        $base = DB::table($joinTable)
            ->join('events', 'events.id', '=', "{$joinTable}.event_id")
            ->where("{$joinTable}.volunteer_id", $userId);

        /**
         * Total joined events
         */
        $totalJoined = (clone $base)
            ->distinct('events.id')
            ->count('events.id');

        /**
         * Present / Absent / Not recorded
         *
         * Priority:
         * 1) attendance_status ('present'/'absent')
         * 2) fallback to present column (1/0)
         * 3) else not recorded
         */
        $presentCount = (clone $base)
            ->where(function ($q) use ($joinTable) {
                $q->where("{$joinTable}.attendance_status", 'present')
                  ->orWhere(function ($q2) use ($joinTable) {
                      $q2->whereNull("{$joinTable}.attendance_status")
                         ->where("{$joinTable}.present", 1);
                  });
            })
            ->count();

        $absentCount = (clone $base)
            ->where(function ($q) use ($joinTable) {
                $q->where("{$joinTable}.attendance_status", 'absent')
                  ->orWhere(function ($q2) use ($joinTable) {
                      $q2->whereNull("{$joinTable}.attendance_status")
                         ->where("{$joinTable}.present", 0);
                  });
            })
            ->count();

        $notRecorded = (clone $base)
            ->whereNull("{$joinTable}.attendance_status")
            ->whereNull("{$joinTable}.present")
            ->count();

        /**
         * Attendance rate based on marked only
         */
        $markedCount = $presentCount + $absentCount;

        $attendanceRate = $markedCount > 0
            ? round(($presentCount / $markedCount) * 100, 1)
            : 0;

        $absentRate = $markedCount > 0
            ? round(($absentCount / $markedCount) * 100, 1)
            : 0;

        /**
         * Chart: events joined by cause
         */
        $byCause = DB::table($joinTable)
            ->join('events', 'events.id', '=', "{$joinTable}.event_id")
            ->leftJoin('causes', 'causes.id', '=', 'events.cause_id')
            ->where("{$joinTable}.volunteer_id", $userId)
            ->selectRaw("COALESCE(causes.name, 'Uncategorized') as cause_name, COUNT(DISTINCT events.id) as total")
            ->groupBy('cause_name')
            ->orderByDesc('total')
            ->get();

        $causeLabels = $byCause->pluck('cause_name')->values();
        $causeTotals = $byCause->pluck('total')->values();

        /**
         * Recent joined events (latest 10)
         */
        $recentEvents = DB::table($joinTable)
            ->join('events', 'events.id', '=', "{$joinTable}.event_id")
            ->leftJoin('causes', 'causes.id', '=', 'events.cause_id')
            ->where("{$joinTable}.volunteer_id", $userId)
            ->select(
                'events.id',
                'events.title',
                'events.event_date',
                'events.time_slot',
                'events.location',
                DB::raw("COALESCE(causes.name, 'Uncategorized') as cause_name"),
                DB::raw("
                    CASE
                        WHEN {$joinTable}.attendance_status = 'present' THEN 'present'
                        WHEN {$joinTable}.attendance_status = 'absent' THEN 'absent'
                        WHEN {$joinTable}.present = 1 THEN 'present'
                        WHEN {$joinTable}.present = 0 THEN 'absent'
                        ELSE 'not_recorded'
                    END as attendance_status
                ")
            )
            ->orderByDesc('events.event_date')
            ->limit(10)
            ->get();

        return view('volunteer.my-report', [
            'totalJoined'     => $totalJoined,
            'presentCount'    => $presentCount,
            'absentCount'     => $absentCount,
            'notRecorded'     => $notRecorded,
            'attendanceRate'  => $attendanceRate,
            'absentRate'      => $absentRate,
            'causeLabels'     => $causeLabels,
            'causeTotals'     => $causeTotals,
            'recentEvents'    => $recentEvents,
        ]);
    }
}
