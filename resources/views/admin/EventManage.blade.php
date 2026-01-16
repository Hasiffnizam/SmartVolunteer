@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Event Management</h1>
        <p class="text-slate-600 mt-1">Search and manage events created in SmartVolunteer.</p>
      </div>

      <a
        href="{{ route('admin.events.create') }}"
        class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-2.5
               bg-slate-900 text-white font-semibold shadow-sm hover:bg-slate-800 transition"
      >
        <span class="text-lg leading-none">+</span>
        <span>New Event</span>
      </a>
    </div>

    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">

      {{-- Search bar --}}
      <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search event name…"
            class="w-full rounded-2xl border border-slate-200 bg-white/80 pl-11 pr-4 py-3
                   text-slate-800 placeholder:text-slate-400 outline-none
                   focus:ring-2 focus:ring-slate-300"
          />
        </div>

        {{-- keep status when searching --}}
        <input type="hidden" name="status" value="{{ request('status', 'all') }}">

        <div class="flex gap-2">
          <button
            type="submit"
            class="rounded-2xl px-4 py-3 font-semibold bg-white border border-slate-200
                   text-slate-700 hover:bg-slate-50 transition"
          >
            Search
          </button>

          @if(request('q') || (request('status') && request('status') !== 'all'))
            <a
              href="{{ url()->current() }}"
              class="rounded-2xl px-4 py-3 font-semibold bg-white border border-slate-200
                     text-slate-700 hover:bg-slate-50 transition"
            >
              Reset
            </a>
          @endif
        </div>
      </form>

      {{-- Filters --}}
      @php
        $activeFilter = request('status', 'all'); // all | upcoming | ongoing | completed
        $qParam = request('q');
        $makeUrl = function($status) use ($qParam) {
          $params = [];
          if (!empty($qParam)) $params['q'] = $qParam;
          if ($status && $status !== 'all') $params['status'] = $status;
          return url()->current() . (count($params) ? ('?' . http_build_query($params)) : '');
        };

        $filterBtn = function($label, $status) use ($activeFilter, $makeUrl) {
          $isActive = ($activeFilter === $status) || ($status === 'all' && ($activeFilter === 'all' || $activeFilter === null));
          $classes = $isActive
            ? 'bg-slate-900 text-white border-slate-900'
            : 'bg-white/80 text-slate-700 border-slate-200 hover:bg-white';
          return [
            'url' => $makeUrl($status),
            'classes' => $classes,
            'label' => $label,
          ];
        };

        $filters = [
          $filterBtn('All', 'all'),
          $filterBtn('Upcoming', 'upcoming'),
          $filterBtn('Ongoing', 'ongoing'),
          $filterBtn('Completed', 'completed'),
        ];
      @endphp

      <div class="mt-4 flex flex-wrap gap-2">
        @foreach($filters as $f)
          <a href="{{ $f['url'] }}"
             class="rounded-2xl border px-4 py-2 text-sm font-semibold transition {{ $f['classes'] }}">
            {{ $f['label'] }}
          </a>
        @endforeach
      </div>

      {{-- List / Empty state --}}
      <div class="mt-6">
        @if($events->count() === 0)
          <div class="rounded-3xl border border-slate-200 bg-white/60 p-10 text-center">
            <div class="mx-auto h-14 w-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center">
              📅
            </div>
            <h3 class="mt-4 text-lg font-bold text-slate-800">No events found</h3>
            <p class="mt-1 text-slate-600">
              Try changing search/filter, or click <span class="font-semibold">New Event</span> to create one.
            </p>

            <a
              href="{{ route('admin.events.create') }}"
              class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3
                     bg-slate-900 text-white font-semibold shadow-sm hover:bg-slate-800 transition"
            >
              <span class="text-lg leading-none">+</span>
              <span>Create Event</span>
            </a>
          </div>
        @else

          <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white/60">
            <div class="grid grid-cols-12 px-5 py-4 text-sm font-semibold text-slate-600 border-b border-slate-200">
              <div class="col-span-6">Event</div>
              <div class="col-span-3">Date / Slot</div>
              <div class="col-span-1">Tasks</div>
              <div class="col-span-2 text-right">Action</div>
            </div>

            <div class="divide-y divide-slate-200">
              @foreach($events as $event)
                @php
                  $causeName = $event->cause?->name ?? '—';

                  $today = \Carbon\Carbon::today();
                  $eventDate = $event->event_date
                      ? \Carbon\Carbon::parse($event->event_date)
                      : null;

                  if (!$eventDate) {
                    $timeStatus = 'Unknown';
                    $statusKey = 'unknown';
                    $statusClasses = 'bg-slate-100 text-slate-600 border-slate-200';
                  } elseif ($eventDate->isFuture()) {
                    $timeStatus = 'Upcoming';
                    $statusKey = 'upcoming';
                    $statusClasses = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                  } elseif ($eventDate->isToday()) {
                    $timeStatus = 'Ongoing';
                    $statusKey = 'ongoing';
                    $statusClasses = 'bg-amber-50 text-amber-700 border-amber-200';
                  } else {
                    $timeStatus = 'Completed';
                    $statusKey = 'completed';
                    $statusClasses = 'bg-rose-50 text-rose-700 border-rose-200';
                  }

                  $rowClass = $statusKey === 'completed'
                      ? 'opacity-60 grayscale hover:opacity-100 hover:grayscale-0'
                      : '';
                @endphp


                <div class="grid grid-cols-12 items-center px-5 py-4 gap-y-3 transition
                            hover:bg-white/70 {{ $rowClass }}">

                  {{-- EVENT INFO --}}
                  <a href="{{ route('admin.events.show', $event->id) }}"
                     class="col-span-12 md:col-span-6 flex items-center gap-3">
                    @if($event->poster_path)
                      <img
                        src="{{ asset('storage/'.$event->poster_path) }}"
                        alt="Poster"
                        class="h-12 w-12 rounded-xl object-cover border border-slate-200 bg-white"
                      />
                    @else
                      <div class="h-12 w-12 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-400">
                        🖼️
                      </div>
                    @endif

                    <div class="min-w-0 flex-1">
                      <div class="flex items-center gap-2">
                        <div class="font-bold text-slate-800 truncate">
                          {{ $event->title }}
                        </div>

                        {{-- Status chip --}}
                        <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold border {{ $statusClasses }}">
                          @if($statusKey === 'ongoing')
                            <span class="mr-2 inline-flex h-2.5 w-2.5 items-center justify-center">
                              <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            </span>
                          @endif
                          {{ $timeStatus }}
                        </span>

                      </div>

                      <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <span class="truncate">{{ $event->location }}</span>

                        <span class="text-slate-300">•</span>

                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                     bg-slate-50 text-slate-700 border border-slate-200">
                          Cause: {{ $causeName }}
                        </span>

                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                     bg-slate-50 text-slate-700 border border-slate-200">
                          Skills: {{ $event->skills_count ?? 0 }}
                        </span>

                      </div>
                    </div>
                  </a>

                  {{-- Date / Slot --}}
                  <div class="col-span-6 md:col-span-3 text-slate-700">
                    <div class="font-semibold">
                      {{ $event->event_date ? $event->event_date->format('d M Y') : '—' }}
                    </div>
                    <div class="text-sm text-slate-500 capitalize">
                      {{ $event->time_slot }}
                    </div>
                  </div>

                  {{-- Tasks --}}
                  <div class="col-span-3 md:col-span-1">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                bg-slate-100 text-slate-700 border border-slate-200">
                      {{ $event->tasks_count }} tasks
                    </span>
                  </div>

                  {{-- Actions --}}
                  <div class="col-span-3 md:col-span-2 flex justify-end gap-2">
                    <a
                      href="{{ route('admin.events.edit', $event->id) }}"
                      class="rounded-xl px-3 py-2 text-sm font-semibold border border-slate-200 bg-white hover:bg-slate-50 transition"
                    >
                      Edit
                    </a>

                    <form
                      method="POST"
                      action="{{ route('admin.events.destroy', $event->id) }}"
                      onsubmit="return confirm('Delete this event? This cannot be undone.')"
                    >
                      @csrf
                      @method('DELETE')
                      <button
                        type="submit"
                        class="rounded-xl px-3 py-2 text-sm font-semibold bg-rose-600 text-white hover:bg-rose-700 transition"
                      >
                        Delete
                      </button>
                    </form>
                  </div>

                </div>
              @endforeach
            </div>
          </div>

          <div class="mt-4">
            {{ $events->links() }}
          </div>
        @endif
      </div>

    </div>
  </div>
</section>
@endsection
