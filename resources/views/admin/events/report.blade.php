@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $event->title }}</h1>
        <p class="text-slate-600 mt-1">
          {{ $event->location }} • {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }} • {{ $event->time_slot }}
        </p>
      </div>

      <a href="{{ route('admin.events.show', $event) }}"
         class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Back
      </a>
    </div>

    {{-- Card --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">

      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Event Report</h2>
          <p class="text-slate-600 text-sm mt-1">Analytics for the selected event (attendance, roles, skills, insights).</p>
        </div>

        <div class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 bg-white/70 border border-white/60 shadow-sm">
          <span class="text-sm text-slate-600">Status:</span>
          <span class="text-sm font-semibold
              {{ $eventStatus === 'Completed' ? 'text-emerald-700 bg-emerald-50' : 'text-sky-700 bg-sky-50' }}
              px-3 py-1 rounded-full">
            {{ $eventStatus }}
          </span>
        </div>
      </div>

      {{-- KPI Cards --}}
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5">
          <p class="text-sm text-slate-600">Total volunteers engaged</p>
          <p class="mt-2 text-3xl font-extrabold text-slate-800">{{ $totalVolunteers }}</p>
        </div>

        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5">
          <p class="text-sm text-slate-600">Attendance</p>
          <p class="mt-2 text-3xl font-extrabold text-slate-800">{{ $attendanceRate }}%</p>
          <p class="text-xs text-slate-500 mt-1">Present / Assigned</p>
        </div>

        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5">
          <p class="text-sm text-slate-600">Task completion</p>
          <p class="mt-2 text-3xl font-extrabold text-slate-800">{{ $avgCompletion }}%</p>
          <p class="text-xs text-slate-500 mt-1">Average completion</p>
        </div>

        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5">
          <p class="text-sm text-slate-600">Cause</p>
          <p class="mt-2 text-lg font-bold text-slate-800">{{ optional($event->cause)->name ?? '-' }}</p>
          <p class="text-xs text-slate-500 mt-1">Event category</p>
        </div>
      </div>

      {{-- Middle Analysis --}}
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Attendance by Role --}}
        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5">
          <h3 class="font-bold text-slate-800">Attendance analysis (by role)</h3>
          <p class="text-sm text-slate-600 mt-1">Present / late / absent based on assigned roles.</p>

          <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="text-slate-500">
                <tr class="border-b border-slate-200/60">
                  <th class="text-left py-2 pr-4">Role</th>
                  <th class="text-right py-2 px-2">Assigned</th>
                  <th class="text-right py-2 px-2">Present</th>
                  <th class="text-right py-2 px-2">Late</th>
                  <th class="text-right py-2 px-2">Absent</th>
                  <th class="text-right py-2 pl-2">Rate</th>
                </tr>
              </thead>
              <tbody class="text-slate-700">
                @forelse($byRole as $r)
                  <tr class="border-b border-slate-100/70">
                    <td class="py-3 pr-4 font-medium">{{ $r['role'] }}</td>
                    <td class="py-3 px-2 text-right">{{ $r['assigned'] }}</td>
                    <td class="py-3 px-2 text-right">{{ $r['present'] }}</td>
                    <td class="py-3 px-2 text-right">{{ $r['late'] }}</td>
                    <td class="py-3 px-2 text-right">{{ $r['absent'] }}</td>
                    <td class="py-3 pl-2 text-right font-semibold">{{ $r['attendance_rate'] }}%</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="py-4 text-slate-500">No data available.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Role & Task Performance --}}
        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5">
          <h3 class="font-bold text-slate-800">Role & task performance</h3>
          <p class="text-sm text-slate-600 mt-1">Compare required slots vs actual assignment and completion.</p>

          <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="text-slate-500">
                <tr class="border-b border-slate-200/60">
                  <th class="text-left py-2 pr-4">Role</th>
                  <th class="text-right py-2 px-2">Required</th>
                  <th class="text-right py-2 px-2">Assigned</th>
                  <th class="text-right py-2 px-2">Present</th>
                  <th class="text-right py-2 pl-2">Completion</th>
                </tr>
              </thead>
              <tbody class="text-slate-700">
                @foreach($rolePerformance as $row)
                  <tr class="border-b border-slate-100/70">
                    <td class="py-3 pr-4 font-medium">
                      {{ $row['role'] }}
                      @if($row['is_underfilled'])
                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                          Underfilled
                        </span>
                      @endif
                    </td>
                    <td class="py-3 px-2 text-right">{{ $row['required_slots'] }}</td>
                    <td class="py-3 px-2 text-right">{{ $row['assigned'] }}</td>
                    <td class="py-3 px-2 text-right">{{ $row['present'] }}</td>
                    <td class="py-3 pl-2 text-right font-semibold">{{ $row['completion_avg'] }}%</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        {{-- Skill Utilization --}}
        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5 lg:col-span-2">
          <h3 class="font-bold text-slate-800">Skill utilization</h3>
          <p class="text-sm text-slate-600 mt-1">How well volunteer skills match the event’s required skills.</p>

          <div class="mt-4 space-y-3">
            @forelse($skillUtilization as $s)
              <div>
                <div class="flex items-center justify-between text-sm">
                  <span class="font-medium text-slate-700">{{ $s['skill'] }}</span>
                  <span class="text-slate-600">
                    @if(is_null($s['percent']))
                      N/A
                    @else
                      {{ $s['percent'] }}% ({{ $s['count'] }}/{{ $totalVolunteers }})
                    @endif
                  </span>

                  <div class="mt-2 h-2.5 rounded-full bg-slate-200/60 overflow-hidden">
                    <div class="h-full bg-slate-700/60"
                        style="width: {{ is_null($s['percent']) ? 0 : $s['percent'] }}%"></div>
                  </div>
              </div>
            @empty
              <p class="text-sm text-slate-500">No required skills set for this event.</p>
            @endforelse
          </div>
        </div>

        {{-- Insights --}}
        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-sm p-5 lg:col-span-2">
          <h3 class="font-bold text-slate-800">Insights & recommendations</h3>
          <p class="text-sm text-slate-600 mt-1">Auto-generated based on attendance, completion, and staffing.</p>

          <div class="mt-4 space-y-3">
            @forelse($insights as $i)
              <div class="rounded-2xl border border-white/60 bg-white/70 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-xl
                    @if($i['type']==='good') bg-emerald-50 text-emerald-700 border border-emerald-100
                    @elseif($i['type']==='warn') bg-amber-50 text-amber-700 border border-amber-100
                    @else bg-sky-50 text-sky-700 border border-sky-100
                    @endif
                  ">
                    @if($i['type']==='good') ✓
                    @elseif($i['type']==='warn') !
                    @else i
                    @endif
                  </span>
                  <p class="text-sm text-slate-700 leading-relaxed">{{ $i['text'] }}</p>
                </div>
              </div>
            @empty
              <p class="text-sm text-slate-500">No insights available.</p>
            @endforelse
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
