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

      <a href="{{ route('volunteer.explore.index') }}"
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
          <div class="relative aspect-[3/4] bg-gradient-to-br from-slate-50 to-slate-100">
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
            Read the role tasks below and pick a slot that matches your skills.
          </div>
        </div>
      </div>

    </div>

    {{-- Role Tasks --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
      <h2 class="text-lg font-bold text-slate-800">Select Role Task Slot</h2>
      <p class="text-slate-600 text-sm mt-1">Slots update live from database. If it’s full, you can’t select it.</p>

      @if($alreadyJoined)
        <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200 p-4 text-slate-800 font-semibold">
          You already joined this event. You can view it in
          <a class="underline" href="{{ route('volunteer.myevents.index') }}">My Events</a>.
        </div>
      @else
        <form method="POST" action="{{ route('volunteer.explore.join', $event) }}" class="mt-5">
          @csrf

          <div class="grid gap-3">
            @foreach($event->roleTasks as $task)
              @php
                $available = max(0, (int)$task->slots - (int)$task->slots_taken);
                $full = $available <= 0;
              @endphp

              <label class="rounded-2xl border shadow-sm p-4 flex items-start gap-4 cursor-pointer transition
                            {{ $full ? 'bg-slate-100/70 border-slate-200 opacity-60 cursor-not-allowed' : 'bg-white/70 border-white/60 hover:bg-white' }}">
                <input
                  type="radio"
                  name="role_task_id"
                  value="{{ $task->id }}"
                  class="mt-1"
                  {{ $full ? 'disabled' : '' }}
                />

                <div class="flex-1">
                  <div class="flex items-center justify-between gap-3">
                    <p class="font-bold text-slate-800">{{ $task->title ?? $task->name ?? 'Role Task' }}</p>

                    @if($full)
                      <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-200 text-slate-700">
                        Full
                      </span>
                    @else
                      <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $available }} slot(s) left
                      </span>
                    @endif
                  </div>

                  @if(!empty($task->description))
                    <p class="text-sm text-slate-600 mt-1">{{ $task->description }}</p>
                  @endif

                  <p class="text-xs text-slate-500 mt-2">
                    Total: {{ (int)$task->slots }} • Taken: {{ (int)$task->slots_taken }}
                  </p>
                </div>
              </label>
            @endforeach
          </div>

          @error('role_task_id')
            <p class="mt-3 text-sm font-semibold text-rose-700">{{ $message }}</p>
          @enderror

          <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">
              Once joined, your name will appear in admin attendance for this event.
            </p>

            <button class="rounded-2xl px-5 py-3 font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
              Join Event
            </button>
          </div>
        </form>
      @endif
    </div>

  </div>
</section>
@endsection
