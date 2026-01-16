@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Report Analysis</h1>
        <p class="text-slate-600 mt-1">System-wide analytics across all events.</p>
      </div>
    </div>

    {{-- KPI Cards --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <p class="text-sm text-slate-600">Total events</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-800">{{ $totalEvents }}</p>
      </div>

      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <p class="text-sm text-slate-600">Completed events</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-800">{{ $completedEvents }}</p>
      </div>

      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <p class="text-sm text-slate-600">Overall attendance</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-800">
          {{ is_null($overallAttendanceRate) ? 'N/A' : $overallAttendanceRate.'%' }}
        </p>
      </div>

      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <p class="text-sm text-slate-600">Overall task completion</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-800">
          {{ is_null($overallTaskCompletion) ? 'N/A' : $overallTaskCompletion.'%' }}
        </p>
      </div>
    </div>

    {{-- Main grid --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-5">

      {{-- Best events --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <h3 class="font-bold text-slate-800">Best events (attendance)</h3>
        <p class="text-sm text-slate-600 mt-1">Top events ranked by attendance rate.</p>

        <div class="mt-4 space-y-3">
          @forelse($bestEvents as $row)
            <div class="rounded-2xl bg-white/70 border border-white/60 shadow-sm p-4">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-800">{{ $row['event']->title }}</p>
                  <p class="text-xs text-slate-600 mt-1">
                    {{ \Carbon\Carbon::parse($row['event']->event_date)->format('d M Y') }} •
                    {{ optional($row['event']->cause)->name ?? 'Unknown cause' }}
                  </p>
                </div>

                <div class="text-right">
                  <p class="text-lg font-extrabold text-slate-800">
                    {{ is_null($row['attendance_rate']) ? 'N/A' : $row['attendance_rate'].'%' }}
                  </p>
                  <p class="text-xs text-slate-500">{{ $row['total'] }} registrations</p>
                </div>
              </div>
            </div>
          @empty
            <p class="text-sm text-slate-500">No event attendance data yet.</p>
          @endforelse
        </div>
      </div>

      {{-- Worst events --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <h3 class="font-bold text-slate-800">Worst events (attendance)</h3>
        <p class="text-sm text-slate-600 mt-1">Events needing improvement based on attendance.</p>

        <div class="mt-4 space-y-3">
          @forelse($worstEvents as $row)
            <div class="rounded-2xl bg-white/70 border border-white/60 shadow-sm p-4">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-800">{{ $row['event']->title }}</p>
                  <p class="text-xs text-slate-600 mt-1">
                    {{ \Carbon\Carbon::parse($row['event']->event_date)->format('d M Y') }} •
                    {{ optional($row['event']->cause)->name ?? 'Unknown cause' }}
                  </p>
                </div>

                <div class="text-right">
                  <p class="text-lg font-extrabold text-slate-800">
                    {{ is_null($row['attendance_rate']) ? 'N/A' : $row['attendance_rate'].'%' }}
                  </p>
                  <p class="text-xs text-slate-500">{{ $row['total'] }} registrations</p>
                </div>
              </div>
            </div>
          @empty
            <p class="text-sm text-slate-500">No event attendance data yet.</p>
          @endforelse
        </div>
      </div>

      {{-- Popular causes --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <h3 class="font-bold text-slate-800">Popular causes</h3>
        <p class="text-sm text-slate-600 mt-1">Ranked by number of events created.</p>

        <div class="mt-4 space-y-3">
          @forelse($popularCauses as $c)
            <div class="flex items-center justify-between rounded-2xl bg-white/70 border border-white/60 shadow-sm p-4">
              <p class="font-medium text-slate-800">{{ $c['cause'] }}</p>
              <span class="text-sm font-semibold text-slate-700">{{ $c['count'] }} events</span>
            </div>
          @empty
            <p class="text-sm text-slate-500">No causes found.</p>
          @endforelse
        </div>
      </div>

      {{-- Volunteer engagement trends --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5">
        <h3 class="font-bold text-slate-800">Volunteer engagement trends</h3>
        <p class="text-sm text-slate-600 mt-1">Monthly assignments & unique volunteers.</p>

        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-slate-500">
              <tr class="border-b border-slate-200/60">
                <th class="text-left py-2 pr-4">Month</th>
                <th class="text-right py-2 px-2">Registrations</th>
                <th class="text-right py-2 pl-2">Unique volunteers</th>
              </tr>
            </thead>
            <tbody class="text-slate-700">
              @forelse($monthlyTrends as $m)
                <tr class="border-b border-slate-100/70">
                  <td class="py-3 pr-4 font-medium">{{ $m['month'] }}</td>
                  <td class="py-3 px-2 text-right">{{ $m['assignments'] }}</td>
                  <td class="py-3 pl-2 text-right">{{ $m['unique_volunteers'] }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="py-4 text-slate-500">No trend data yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Insights --}}
      <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-5 lg:col-span-2">
        <h3 class="font-bold text-slate-800">System-level insights</h3>
        <p class="text-sm text-slate-600 mt-1">Auto-generated findings based on overall performance.</p>

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
</section>
@endsection
