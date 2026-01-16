@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">My Volunteer Report</h1>
        <p class="text-slate-600 mt-1">Real-time summary of your volunteering activities.</p>
      </div>
    </div>

    {{-- Summary cards --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-5">
        <p class="text-sm text-slate-500">Events Joined</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalJoined }}</p>
      </div>

      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-5">
        <p class="text-sm text-slate-500">Attended (Present)</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $presentCount }}</p>
      </div>

      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-5">
        <p class="text-sm text-slate-500">Missed (Absent)</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $absentCount }}</p>
      </div>

      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-5">
        <p class="text-sm text-slate-500">Not Recorded Yet</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $notRecorded }}</p>
      </div>
    </div>

    {{-- Attendance rate --}}
    <div class="mt-4 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-lg font-bold text-slate-800">Attendance Rate</h2>
          <p class="text-sm text-slate-600">Based on events that have attendance marked.</p>
        </div>
        <div class="text-right">
          <div class="text-sm text-slate-600">
            Present: <span class="font-semibold text-slate-900">{{ $attendanceRate }}%</span>
          </div>
          <div class="text-sm text-slate-600">
            Absent: <span class="font-semibold text-slate-900">{{ $absentRate }}%</span>
          </div>
        </div>
      </div>

      @php
        $rate = (int) max(0, min(100, round($attendanceRate)));
      @endphp

      <div class="mt-4">
        <div class="h-3 w-full rounded-full bg-slate-100 overflow-hidden">
          <div class="h-3 bg-emerald-500" @style(['width' => $rate.'%'])></div>
        </div>
        <div class="mt-2 text-xs text-slate-500">
          Note: “Not recorded yet” doesn’t affect this percentage.
        </div>
      </div>
    </div>

    {{-- Charts + table --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- Bar chart --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-bold text-slate-800">Events Joined by Cause</h2>
          <span class="text-xs text-slate-500">auto-updated</span>
        </div>

        <div class="mt-4">
          <canvas id="causeBarChart" height="130"></canvas>
        </div>

        @if(count($causeLabels) === 0)
          <p class="mt-3 text-sm text-slate-500">No joined events found.</p>
        @endif
      </div>

      {{-- Recent events --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800">Recent Joined Events</h2>
        <p class="text-sm text-slate-600 mt-1">Latest 10 events you joined.</p>

        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-slate-500 border-b border-slate-200/70">
                <th class="py-2 pr-3">Event</th>
                <th class="py-2 pr-3">Date</th>
                <th class="py-2 pr-3">Cause</th>
                <th class="py-2">Attendance</th>
              </tr>
            </thead>

            <tbody>
              @forelse($recentEvents as $e)
                @php
                  $status = $e->attendance_status;

                  $badge = 'bg-slate-100 text-slate-700';
                  $label = 'Not Recorded';

                  if ($status === 'present') {
                    $badge = 'bg-emerald-100 text-emerald-700';
                    $label = 'Present';
                  } elseif ($status === 'absent') {
                    $badge = 'bg-rose-100 text-rose-700';
                    $label = 'Absent';
                  }
                @endphp

                <tr class="border-b border-slate-200/50">
                  <td class="py-2 pr-3 font-medium text-slate-800">{{ $e->title }}</td>
                  <td class="py-2 pr-3 text-slate-700">{{ \Carbon\Carbon::parse($e->event_date)->format('d M Y') }}</td>
                  <td class="py-2 pr-3 text-slate-700">{{ $e->cause_name }}</td>
                  <td class="py-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge }}">
                      {{ $label }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="py-4 text-slate-500">No events to show.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

{{-- ✅ Lint-friendly JSON payloads --}}
<script type="application/json" id="causeLabelsJson">@json($causeLabels)</script>
<script type="application/json" id="causeTotalsJson">@json($causeTotals)</script>

<script>
  const causeLabels = JSON.parse(document.getElementById('causeLabelsJson').textContent || '[]');
  const causeTotals = JSON.parse(document.getElementById('causeTotalsJson').textContent || '[]');

  const ctx = document.getElementById('causeBarChart');
  if (ctx && causeLabels.length) {
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: causeLabels,
        datasets: [{
          label: 'Events Joined',
          data: causeTotals,
          borderWidth: 1,
          borderRadius: 10
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });
  }
</script>
@endsection
