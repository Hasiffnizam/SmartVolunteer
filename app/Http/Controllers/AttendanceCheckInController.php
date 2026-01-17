<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceCheckInController extends Controller
{
    private function slotStartTime(string $timeSlot): string
    {
        return match ($timeSlot) {
            'morning' => '09:00:00',
            'evening' => '14:00:00',
            'night'   => '20:00:00',
            default   => '09:00:00',
        };
    }

    private function checkInAllowed(Event $event): bool
    {
        $tz = config('app.timezone');

        $start = Carbon::parse(
            $event->event_date->format('Y-m-d') . ' ' . $this->slotStartTime($event->time_slot),
            $tz
        );

        // window: start - 15 min to start + 60 min
        $windowStart = $start->copy()->subDays(7);
        $windowEnd   = $start->copy()->addDays(7);


        return Carbon::now($tz)->between($windowStart, $windowEnd);
    }

    // =========================
    // Email confirm (signed URL)
    // =========================
    public function confirmFromEmail(Event $event, EventRegistration $registration)
    {
        // Security: ensure this registration belongs to this event
        if ((int) $registration->event_id !== (int) $event->id) {
            return response()->view('attendance.error', ['message' => 'Invalid attendance link.'], 403);
        }

        if (!$this->checkInAllowed($event)) {
            return response()->view('attendance.error', ['message' => 'Check-in is not available at this time.'], 403);
        }

        // Update same row (no duplicates)
        $registration->update([
            'attendance_status' => 'present',
            'present' => true,
            'checked_in_at' => now(),
            'check_in_method' => 'email',
        ]);

        return view('attendance.confirmed', [
            'title' => 'Attendance recorded',
            'subtitle' => 'Attendance recorded successfully.',
        ]);
    }

    // =========================
    // QR flow (logged-in volunteer)
    // =========================
    public function showQrCheckIn(Event $event)
    {
        if (!$this->checkInAllowed($event)) {
            return response()->view('attendance.error', [
                'message' => 'Check-in is not available at this time.'
            ], 403);
        }

        $volunteerId = Auth::id();

        $reg = EventRegistration::where('event_id', $event->id)
            ->where('volunteer_id', $volunteerId)
            ->first();

        if (!$reg) {
            return response()->view('attendance.error', [
                'message' => 'You are not registered for this event.'
            ], 403);
        }

        // ✅ NEW: already checked in (admin / email / qr)
        if (
            $reg->attendance_status === 'present' ||
            (int) $reg->present === 1 ||
            !is_null($reg->checked_in_at)
        ) {
            return view('attendance.confirmed', [
                'title' => 'Already Checked In',
                'subtitle' => 'Your attendance has already been recorded.',
            ]);
        }

        // Otherwise allow QR confirmation
        return view('volunteer.checkin', [
            'event' => $event,
            'registration' => $reg,
        ]);
    }

    public function confirmQrCheckIn(Request $request, Event $event)
    {
        if (!$this->checkInAllowed($event)) {
            return response()->view('attendance.error', ['message' => 'Check-in is not available at this time.'], 403);
        }

        $volunteerId = Auth::id(); // ✅ IDE-safe + correct

        $reg = EventRegistration::where('event_id', $event->id)
            ->where('volunteer_id', $volunteerId)
            ->first();

        if (!$reg) {
            return response()->view('attendance.error', ['message' => 'You are not registered for this event.'], 403);
        }

        // If already present, just show success
        if ($reg->attendance_status === 'present') {
            return view('attendance.confirmed', [
                'title' => 'Already checked in',
                'subtitle' => 'You are already marked as present.',
            ]);
        }

        $reg->update([
            'attendance_status' => 'present',
            'present' => true,
            'checked_in_at' => now(),
            'check_in_method' => 'qr',
        ]);

        return view('attendance.confirmed', [
            'title' => 'Check-in successful',
            'subtitle' => 'You are marked as present.',
        ]);
    }
}
