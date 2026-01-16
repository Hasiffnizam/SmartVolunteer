@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-5xl">

    @php
      $posterUrl = $event->poster_path ? asset('storage/'.$event->poster_path) : null;
      $dateLabel = \Carbon\Carbon::parse($event->event_date)->format('d M Y');
    @endphp

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $event->title }}</h1>

        <div class="mt-2 flex flex-wrap items-center gap-2 text-slate-600">
          <span class="inline-flex items-center gap-2">
            <span class="h-8 w-8 grid place-items-center rounded-xl bg-white/70 border border-white/60">📍</span>
            <span class="font-semibold text-slate-700">{{ $event->location }}</span>
          </span>

          <span class="text-slate-300">•</span>

          <span class="inline-flex items-center gap-2">
            <span class="h-8 w-8 grid place-items-center rounded-xl bg-white/70 border border-white/60">📅</span>
            <span>{{ $dateLabel }}</span>
          </span>

          <span class="text-slate-300">•</span>

          <span class="inline-flex items-center gap-2">
            <span class="h-8 w-8 grid place-items-center rounded-xl bg-white/70 border border-white/60">⏰</span>
            <span class="font-semibold text-slate-800">{{ $event->timeSlotLabel() }}</span>
          </span>
        </div>
      </div>

      <a href="{{ route('volunteer.myevents.index') }}"
         class="shrink-0 rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Back
      </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
      <div class="mt-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 font-semibold">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="mt-5 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 font-semibold">
        {{ session('error') }}
      </div>
    @endif

    {{-- Top: Poster + About --}}
    <div class="mt-6 grid gap-5 md:grid-cols-5">

      {{-- Poster --}}
      <div class="md:col-span-2">
        <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg overflow-hidden">
          {{-- Use a taller poster box + object-contain so FULL poster is visible --}}
          <div class="relative h-[420px] bg-gradient-to-br from-slate-50 to-slate-100">
            @if($posterUrl)
              <img
                src="{{ $posterUrl }}"
                alt="Event poster"
                class="absolute inset-0 h-full w-full object-contain p-3"
                loading="lazy"
                onerror="this.style.display='none'; this.parentElement.querySelector('.fallback').classList.remove('hidden');"
              />
            @endif

            {{-- fallback --}}
            <div class="fallback {{ $posterUrl ? 'hidden' : '' }} absolute inset-0 grid place-items-center">
              <div class="text-center px-6">
                <div class="mx-auto h-14 w-14 rounded-2xl bg-white/80 border border-slate-200 grid place-items-center shadow-sm">
                  🗓️
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-600">No poster uploaded</p>
              </div>
            </div>
          </div>

          <div class="p-4">
            <p class="text-xs text-slate-500">
              Poster is uploaded by admin. If it’s missing, the event still runs as usual.
            </p>
          </div>
        </div>
      </div>

      {{-- About this event --}}
      <div class="md:col-span-3">
        <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
          <div class="flex items-center gap-2">
            <div class="h-10 w-10 grid place-items-center rounded-2xl bg-white/70 border border-white/60">ℹ️</div>
            <h2 class="text-lg font-bold text-slate-800">About this event</h2>
          </div>

          @if($event->description)
            <p class="mt-4 text-slate-700 whitespace-pre-line leading-relaxed">
              {{ $event->description }}
            </p>
          @else
            <p class="mt-4 text-slate-400 italic">
              No description provided.
            </p>
          @endif

          <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700">
            <span class="font-semibold">Tip:</span>
            Check your joined slot below. If you need changes, contact the admin.
          </div>
        </div>
      </div>

    </div>

    {{-- Your Joined Slot (replaces Select Role Task + removes Role Tasks & Slots) --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
      <div class="flex items-center gap-2">
        <div class="h-10 w-10 grid place-items-center rounded-2xl bg-white/70 border border-white/60">✅</div>
        <h2 class="text-lg font-bold text-slate-800">Your Joined Slot</h2>
      </div>

      @if($myRegistration && $myRegistration->roleTask)
        <div class="mt-4 rounded-2xl bg-emerald-50 border border-emerald-200 p-5">
          <p class="text-base font-bold text-emerald-900">
            {{ $myRegistration->roleTask->title ?? $myRegistration->roleTask->name ?? 'Role Task' }}
          </p>

          <div class="mt-2 text-sm text-emerald-900/80">
            <span class="font-semibold">Joined at:</span>
            {{ $myRegistration->joined_at?->format('d M Y, h:i A') }}
          </div>

          <div class="mt-3 text-xs text-emerald-900/70">
            Your slot is locked to prevent overbooking. If you need to switch tasks, please contact the admin.
          </div>
        </div>
      @else
        <div class="mt-4 rounded-2xl bg-rose-50 border border-rose-200 p-5">
          <p class="font-semibold text-rose-800">
            No registration record found for your account.
          </p>
          <p class="mt-1 text-sm text-rose-700">
            Try going back to Explore Events and join the event again.
          </p>
        </div>
      @endif
    </div>

  </div>
</section>
@endsection
