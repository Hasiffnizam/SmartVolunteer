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

        // ✅ Your real join table + columns (from your screenshot)
        $joinTable = 'event_registrations';
        $userCol = 'volunteer_id';      // links to users.id
        $presentCol = 'present';        // 1 = present, 0 = absent, null = not recorded
        $causeNameCol = 'name';         // causes.name

        /**
         * Base query: joined events for this volunteer
         */
        $joinedEventsQuery = DB::table($joinTable)
            ->join('events', 'events.id', '=', "{$joinTable}.event_id")
            ->where("{$joinTable}.{$userCol}", $userId);

        $totalJoined = (clone $joinedEventsQuery)
            ->distinct('events.id')
            ->count('events.id');

        /**
         * Attendance counts from event_registrations.present
         */
        $presentCount = (clone $joinedEventsQuery)
            ->where("{$joinTable}.{$presentCol}", 1)
            ->count();

        $absentCount = (clone $joinedEventsQuery)
            ->where("{$joinTable}.{$presentCol}", 0)
            ->count();

        $notRecorded = (clone $joinedEventsQuery)
            ->whereNull("{$joinTable}.{$presentCol}")
            ->count();

        // Attendance % based only on marked (present+absent)
        $markedCount = $presentCount + $absentCount;

        $attendanceRate = $markedCount > 0
            ? round(($presentCount / $markedCount) * 100, 1)
            : 0;

        $absentRate = $markedCount > 0
            ? round(($absentCount / $markedCount) * 100, 1)
            : 0;

        /**
         * Chart: joined events by cause
         */
        $byCause = DB::table($joinTable)
            ->join('events', 'events.id', '=', "{$joinTable}.event_id")
            ->leftJoin('causes', 'causes.id', '=', 'events.cause_id')
            ->where("{$joinTable}.{$userCol}", $userId)
            ->selectRaw("COALESCE(causes.$causeNameCol, 'Uncategorized') as cause_name, COUNT(DISTINCT events.id) as total")
            ->groupBy('cause_name')
            ->orderByDesc('total')
            ->get();

        $causeLabels = $byCause->pluck('cause_name')->values();
        $causeTotals = $byCause->pluck('total')->values();

        /**
         * Recent joined events list (latest 10)
         */
        $recentEvents = DB::table($joinTable)
            ->join('events', 'events.id', '=', "{$joinTable}.event_id")
            ->leftJoin('causes', 'causes.id', '=', 'events.cause_id')
            ->where("{$joinTable}.{$userCol}", $userId)
            ->select(
                'events.id',
                'events.title',
                'events.event_date',
                'events.time_slot',
                'events.location',
                DB::raw("COALESCE(causes.$causeNameCol, 'Uncategorized') as cause_name"),
                DB::raw("
                    CASE
                      WHEN {$joinTable}.{$presentCol} = 1 THEN 'present'
                      WHEN {$joinTable}.{$presentCol} = 0 THEN 'absent'
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
