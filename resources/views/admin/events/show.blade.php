@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $event->title }}</h1>
        <p class="text-slate-600 mt-1">
          {{ $event->location }} • {{ $event->event_date?->format('d M Y') ?? '—' }} • {{ ucfirst($event->time_slot) }}
        </p>
      </div>

      <a href="{{ route('admin.events.checkin', $event) }}"
        class="rounded-2xl px-4 py-2.5 font-semibold text-white shadow bg-gradient-to-r from-pink-500 to-orange-500 hover:opacity-95 transition">
        Open QR Check-in
      </a>

      <a href="{{ route('admin.events.index') }}"
         class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Back
      </a>

    </div>

    @if($event->description)
      <div class="mt-4 rounded-2xl bg-white/70 border border-white/60 p-4">
        <div class="text-sm font-semibold text-slate-700">Description</div>
        <p class="mt-1 text-slate-700 whitespace-pre-line">{{ $event->description }}</p>
      </div>
    @endif


    {{-- Main Card --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Poster --}}
        <div class="lg:col-span-4">
          <div class="rounded-3xl border border-slate-200 bg-white/60 p-4">
            <div class="text-sm font-semibold text-slate-700 mb-3">Event Poster</div>

            <div class="aspect-[4/5] w-full rounded-2xl border border-slate-200 bg-white overflow-hidden">
              @if($event->poster_path)
                <img
                  src="{{ asset('storage/'.$event->poster_path) }}"
                  alt="Event Poster"
                  class="h-full w-full object-cover"
                >
              @else
                <div class="h-full w-full grid place-items-center text-slate-400">
                  <div class="text-center px-6">
                    <div class="text-4xl mb-2">🖼️</div>
                    <div class="font-semibold">No Poster</div>
                    <div class="text-sm">Upload a poster when editing the event.</div>
                  </div>
                </div>
              @endif
            </div>

          </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-8 space-y-5">

          {{-- Quick stats row --}}
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white/60 border border-white/60 p-4">
              <div class="text-xs font-semibold text-slate-500 uppercase">Cause</div>
              <div class="mt-1 font-semibold text-slate-800">{{ $event->cause?->name ?? '—' }}</div>
            </div>

            <div class="rounded-2xl bg-white/60 border border-white/60 p-4">
              <div class="text-xs font-semibold text-slate-500 uppercase">Status</div>
              <div class="mt-1 font-semibold text-slate-800">{{ ucfirst($event->status) }}</div>
            </div>

            <div class="rounded-2xl bg-white/60 border border-white/60 p-4">
              <div class="text-xs font-semibold text-slate-500 uppercase">Tasks</div>
              <div class="mt-1 font-semibold text-slate-800">{{ $event->tasks_count ?? 0 }}</div>
            </div>
          </div>

          {{-- Skills --}}
          <div class="rounded-3xl bg-white/60 border border-white/60 p-5">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="text-sm font-semibold text-slate-700">Skills Needed</div>
                <div class="text-xs text-slate-500 mt-0.5">Skills volunteers should have for this event.</div>
              </div>
            </div>

            <div class="mt-3">
              @if($event->skills->count())
                <div class="flex flex-wrap gap-2">
                  @foreach($event->skills as $skill)
                    <span class="rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 border border-slate-200 text-slate-700">
                      {{ $skill->name }}
                    </span>
                  @endforeach
                </div>
              @else
                <div class="text-slate-500">—</div>
              @endif
            </div>
          </div>

          {{-- Optional: Small info section --}}
          <div class="rounded-3xl bg-white/60 border border-white/60 p-5">
            <div class="text-sm font-semibold text-slate-700">Event Info</div>

            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 text-slate-700">
              <div>
                <div class="text-xs font-semibold text-slate-500 uppercase">Date</div>
                <div class="mt-1 font-semibold text-slate-800">
                  {{ $event->event_date?->format('d M Y') ?? '—' }}
                </div>
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-500 uppercase">Time Slot</div>
                <div class="mt-1 font-semibold text-slate-800">{{ ucfirst($event->time_slot) }}</div>
              </div>

              <div class="sm:col-span-2">
                <div class="text-xs font-semibold text-slate-500 uppercase">Location</div>
                <div class="mt-1 font-semibold text-slate-800">{{ $event->location ?? '—' }}</div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</section>
@endsection
