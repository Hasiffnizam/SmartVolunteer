@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Explore Events</h1>
        <p class="text-slate-600 mt-1">Browse events created by admins and join by selecting a role task slot.</p>
      </div>

      {{-- Search --}}
      <form method="GET" class="flex gap-2 w-full sm:w-auto">
        <div class="relative w-full sm:w-[420px]">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
          <input
            name="q"
            value="{{ $q }}"
            placeholder="Search title or location..."
            class="w-full rounded-2xl bg-white/70 backdrop-blur border border-white/60 shadow-sm pl-11 pr-4 py-3
                   text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-slate-300"
          />
        </div>
        <button class="rounded-2xl px-5 py-3 font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
          Search
        </button>
      </form>
    </div>

    {{-- Grid --}}
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($events as $event)

        @php
          // Poster URL (uses storage path)
          $posterUrl = $event->poster_path
            ? asset('storage/'.$event->poster_path)
            : null;

          // Status badge (simple: based on date only)
          $today = \Carbon\Carbon::today();
          $d = \Carbon\Carbon::parse($event->event_date);

          if ($d->isSameDay($today)) { $status = 'Ongoing'; $statusClass = 'bg-amber-100 text-amber-800 border border-amber-200'; }
          elseif ($d->isFuture())    { $status = 'Upcoming'; $statusClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200'; }
          else                      { $status = 'Completed'; $statusClass = 'bg-slate-200 text-slate-700 border border-slate-300'; }
        @endphp

        <a href="{{ route('volunteer.explore.show', $event) }}"
           class="group rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg overflow-hidden
                  hover:shadow-xl transition hover:-translate-y-0.5">

          <div class="flex flex-col sm:flex-row">

            {{-- Poster (LEFT) --}}
            <div class="relative w-full h-44 sm:h-auto sm:w-44 bg-slate-100 shrink-0 overflow-hidden">
              @if($posterUrl)
                <img
                  src="{{ $posterUrl }}"
                  alt="Poster"
                  class="h-full w-full object-cover"
                  loading="lazy"
                  onerror="this.style.display='none'; this.parentElement.querySelector('.fallback').classList.remove('hidden');"
                />
              @endif

              {{-- Fallback --}}
              <div class="fallback {{ $posterUrl ? 'hidden' : '' }} absolute inset-0 grid place-items-center bg-gradient-to-br from-slate-50 to-slate-100">
                <div class="text-center px-4">
                  <div class="mx-auto h-12 w-12 rounded-2xl bg-white/80 border border-slate-200 grid place-items-center shadow-sm">
                    🗓️
                  </div>
                  <p class="mt-3 text-sm font-semibold text-slate-600">No poster uploaded</p>
                </div>
              </div>

              {{-- Badges --}}
              <div class="absolute top-3 left-3 flex gap-2">
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                  {{ $status }}
                </span>
              </div>

              <div class="absolute top-3 right-3">
                <span class="rounded-full px-3 py-1 text-xs font-semibold bg-white/90 text-slate-700 border border-white/60 shadow-sm">
                  {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                </span>
              </div>
            </div>

            {{-- Body (RIGHT) --}}
            <div class="flex-1 p-5">
              <h3 class="text-lg font-bold text-slate-800 group-hover:text-slate-900 line-clamp-2">
                {{ $event->title }}
              </h3>

              <p class="text-sm text-slate-600 mt-2 flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/70 border border-white/60">📍</span>
                <span class="line-clamp-1">{{ $event->location }}</span>
              </p>

              <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
                <div class="flex items-center gap-2 text-slate-600">
                  <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/70 border border-white/60">⏰</span>
                  <span class="font-semibold text-slate-800">{{ $event->timeSlotLabel() }}</span>
                </div>

                <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                  Joined: <span class="font-bold">{{ $event->registrations_count ?? 0 }}</span>
                </span>
              </div>
            </div>

          </div>
        </a>

      @empty
        <div class="col-span-full rounded-3xl bg-white/70 border border-white/60 shadow-lg p-10 text-center">
          <div class="mx-auto h-14 w-14 rounded-2xl bg-white border border-slate-200 grid place-items-center">🧭</div>
          <h3 class="mt-4 text-lg font-bold text-slate-800">No events found</h3>
          <p class="mt-1 text-slate-600">Try a different keyword.</p>
        </div>
      @endforelse
    </div>

    <div class="mt-6">
      {{ $events->links() }}
    </div>

  </div>
</section>
@endsection