@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    @php
      // UI helpers
      $activePill = 'bg-slate-900 text-white shadow-sm';
      $idlePill   = 'bg-white/70 text-slate-700 border border-white/60 hover:bg-white/90';
    @endphp

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">My Events</h1>
        <p class="text-slate-600 mt-1">Events you joined. Filter by status and search quickly.</p>
      </div>

      {{-- Filters + Search --}}
      <form method="GET" class="w-full lg:w-auto">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

          {{-- Pills --}}
          <div class="flex flex-wrap gap-2">
            <a href="{{ route('volunteer.myevents.index', ['filter' => 'upcoming', 'q' => $q]) }}"
               class="rounded-2xl px-4 py-2 text-sm font-semibold transition {{ $filter==='upcoming' ? $activePill : $idlePill }}">
              Upcoming
            </a>

            <a href="{{ route('volunteer.myevents.index', ['filter' => 'ongoing', 'q' => $q]) }}"
               class="rounded-2xl px-4 py-2 text-sm font-semibold transition {{ $filter==='ongoing' ? $activePill : $idlePill }}">
              Ongoing
            </a>

            <a href="{{ route('volunteer.myevents.index', ['filter' => 'completed', 'q' => $q]) }}"
               class="rounded-2xl px-4 py-2 text-sm font-semibold transition {{ $filter==='completed' ? $activePill : $idlePill }}">
              Completed
            </a>

            <a href="{{ route('volunteer.myevents.index', ['filter' => 'all', 'q' => $q]) }}"
               class="rounded-2xl px-4 py-2 text-sm font-semibold transition {{ $filter==='all' ? $activePill : $idlePill }}">
              All
            </a>
          </div>

          {{-- Search --}}
          <div class="flex gap-2 w-full lg:w-auto">
            <div class="relative w-full lg:w-[360px]">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
              <input
                name="q"
                value="{{ $q }}"
                placeholder="Search title or location..."
                class="w-full rounded-2xl bg-white/70 backdrop-blur border border-white/60 shadow-sm
                       pl-11 pr-4 py-2.5 text-slate-800 placeholder:text-slate-400
                       outline-none focus:ring-2 focus:ring-slate-300"
              />
            </div>

            <input type="hidden" name="filter" value="{{ $filter }}">

            <button
              class="shrink-0 rounded-2xl px-5 py-2.5 font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
              Search
            </button>
          </div>

        </div>
      </form>
    </div>

    {{-- List --}}
    <div class="mt-6 grid gap-4">
      @forelse($events as $event)
        @php
          $posterUrl = $event->poster_path ? asset('storage/'.$event->poster_path) : null;

          $d = \Carbon\Carbon::parse($event->event_date);
          $dateLabel = $d->format('d M Y');

          // Your filter uses date-based status. We'll show badge that matches.
          $today = \Carbon\Carbon::today();
          if ($d->isSameDay($today)) {
            $badgeText = 'Ongoing';
            $badgeClass = 'bg-amber-100 text-amber-800 border border-amber-200';
          } elseif ($d->isFuture()) {
            $badgeText = 'Upcoming';
            $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
          } else {
            $badgeText = 'Completed';
            $badgeClass = 'bg-slate-200 text-slate-700 border border-slate-300';
          }
        @endphp

        <a href="{{ route('volunteer.myevents.show', $event) }}"
           class="group rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg
                  hover:shadow-xl transition overflow-hidden">

          <div class="flex flex-col sm:flex-row">

            {{-- Thumb --}}
            <div class="sm:w-56">
              <div class="relative h-40 sm:h-full bg-gradient-to-br from-slate-50 to-slate-100">
                @if($posterUrl)
                  <img
                    src="{{ $posterUrl }}"
                    alt="Poster"
                    class="absolute inset-0 h-full w-full object-cover"
                    loading="lazy"
                    onerror="this.style.display='none'; this.parentElement.querySelector('.fallback').classList.remove('hidden');"
                  />
                @endif

                {{-- fallback --}}
                <div class="fallback {{ $posterUrl ? 'hidden' : '' }} absolute inset-0 grid place-items-center">
                  <div class="text-center px-4">
                    <div class="mx-auto h-12 w-12 rounded-2xl bg-white/80 border border-slate-200 grid place-items-center shadow-sm">
                      🗓️
                    </div>
                    <p class="mt-3 text-xs font-semibold text-slate-600">No poster</p>
                  </div>
                </div>

                {{-- Status badge --}}
                <div class="absolute top-3 left-3">
                  <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                    {{ $badgeText }}
                  </span>
                </div>
              </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 p-5">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                <div class="min-w-0">
                  <h3 class="text-lg font-bold text-slate-800 group-hover:text-slate-900 line-clamp-1">
                    {{ $event->title }}
                  </h3>

                  <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2">
                      <span class="h-8 w-8 grid place-items-center rounded-xl bg-white/70 border border-white/60">📍</span>
                      <span class="font-semibold text-slate-700 line-clamp-1">{{ $event->location }}</span>
                    </span>
                  </div>

                  {{-- Small preview --}}
                  <div class="mt-3">
                    @if($event->description)
                      <p class="text-sm text-slate-600 line-clamp-2">
                        {{ $event->description }}
                      </p>
                    @else
                      <p class="text-sm text-slate-400 italic">
                        No description provided.
                      </p>
                    @endif
                  </div>
                </div>

                {{-- Chips --}}
                <div class="flex shrink-0 flex-wrap gap-2 sm:flex-col sm:items-end">
                  <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $dateLabel }}
                  </span>

                  <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $event->timeSlotLabel() }}
                  </span>
                </div>
              </div>

              {{-- Footer row --}}
              <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                <span class="inline-flex items-center gap-2">
                  <span class="h-7 w-7 grid place-items-center rounded-xl bg-white/70 border border-white/60">👆</span>
                  Click to view your joined slot
                </span>
                
              </div>
            </div>

          </div>
        </a>
      @empty
        <div class="rounded-3xl bg-white/70 border border-white/60 shadow-lg p-10 text-center">
          <div class="mx-auto h-14 w-14 rounded-2xl bg-white border border-slate-200 grid place-items-center">⭐</div>
          <h3 class="mt-4 text-lg font-bold text-slate-800">No events found</h3>
          <p class="mt-1 text-slate-600">Try switching the filter or searching different keywords.</p>
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
      {{ $events->links() }}
    </div>

  </div>
</section>
@endsection
