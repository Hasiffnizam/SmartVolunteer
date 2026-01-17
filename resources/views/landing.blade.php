@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4">

  {{-- HERO --}}
  <section class="min-h-[85vh] flex items-center justify-center">
    <div class="text-center max-w-3xl">

      <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/70 border border-white/60 text-xs font-semibold text-slate-700 shadow-sm">
        ✨ One platform for volunteering & event management
      </p>

      <h1 class="mt-6 text-4xl md:text-6xl font-extrabold tracking-tight leading-tight">
        Volunteer smarter,
        <span class="bg-gradient-to-r from-orange-500 to-pink-500 bg-clip-text text-transparent">
          impact bigger.
        </span>
      </h1>

      <p class="mt-5 text-base md:text-lg text-slate-600 leading-relaxed">
        Join meaningful events, confirm attendance easily, and track your volunteering record — all in one simple place.
      </p>

      <div class="mt-6 flex flex-col md:flex-row items-center justify-center gap-3">
        <a href="{{ route('register.show') }}"
           class="px-7 py-3 rounded-2xl text-white font-semibold shadow-sm transition
                  bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
                  hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
          Get Started
        </a>
      </div>

      {{-- quick benefits --}}
      <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3 text-left">
        <div class="rounded-2xl bg-white/60 border border-white/60 p-4 shadow-sm">
          <div class="font-semibold text-slate-900">✅ Easy Join</div>
          <div class="text-sm text-slate-600 mt-1">Browse & join events in seconds.</div>
        </div>
        <div class="rounded-2xl bg-white/60 border border-white/60 p-4 shadow-sm">
          <div class="font-semibold text-slate-900">📍 Fast Check-in</div>
          <div class="text-sm text-slate-600 mt-1">QR / email attendance confirmation.</div>
        </div>
        <div class="rounded-2xl bg-white/60 border border-white/60 p-4 shadow-sm">
          <div class="font-semibold text-slate-900">📊 Track Impact</div>
          <div class="text-sm text-slate-600 mt-1">See your participation report anytime.</div>
        </div>
      </div>

    </div>
  </section>

  {{-- FEATURES --}}
  <section id="features" class="py-12">
    <div class="text-center mb-8">
      <h2 class="text-2xl md:text-3xl font-extrabold">Why SmartVolunteer?</h2>
      <p class="mt-2 text-slate-600">Built to help volunteers participate easily and help organizers run events smoothly.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div class="rounded-3xl bg-white/60 border border-white/60 p-7 shadow-sm text-left">
        <div class="text-lg font-extrabold">🔎 Explore events by cause</div>
        <p class="mt-2 text-slate-600 leading-relaxed">
          Find opportunities that match your interests (health, environment, community, etc.).
        </p>
      </div>

      <div class="rounded-3xl bg-white/60 border border-white/60 p-7 shadow-sm text-left">
        <div class="text-lg font-extrabold">🧩 Structured volunteer matching</div>
        <p class="mt-2 text-slate-600 leading-relaxed">
          Assignments can be organized based on skills, availability, and event roles for better coordination.
        </p>
      </div>

      <div class="rounded-3xl bg-white/60 border border-white/60 p-7 shadow-sm text-left">
        <div class="text-lg font-extrabold">📌 QR / Email attendance</div>
        <p class="mt-2 text-slate-600 leading-relaxed">
          Confirm attendance quickly using self check-in (QR) or a reminder email link. No duplicate records.
        </p>
      </div>

      <div class="rounded-3xl bg-white/60 border border-white/60 p-7 shadow-sm text-left">
        <div class="text-lg font-extrabold">📄 Personal volunteering report</div>
        <p class="mt-2 text-slate-600 leading-relaxed">
          Track joined events, attended vs missed, and view your recent participation history.
        </p>
      </div>
    </div>
  </section>

  {{-- HOW IT WORKS --}}
  <section id="how" class="py-12">
    <div class="rounded-3xl bg-white/60 border border-white/60 p-8 shadow-sm">
      <h2 class="text-2xl md:text-3xl font-extrabold text-center">How it works</h2>

      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="rounded-2xl bg-white/70 border border-white/60 p-6">
          <div class="text-sm font-semibold text-orange-600">Step 1</div>
          <div class="text-lg font-extrabold mt-1">Create your profile</div>
          <p class="text-slate-600 mt-2">Register once and keep your volunteer details in one place.</p>
        </div>
        <div class="rounded-2xl bg-white/70 border border-white/60 p-6">
          <div class="text-sm font-semibold text-orange-600">Step 2</div>
          <div class="text-lg font-extrabold mt-1">Join an event</div>
          <p class="text-slate-600 mt-2">Explore events and confirm participation with one click.</p>
        </div>
        <div class="rounded-2xl bg-white/70 border border-white/60 p-6">
          <div class="text-sm font-semibold text-orange-600">Step 3</div>
          <div class="text-lg font-extrabold mt-1">Check in & track</div>
          <p class="text-slate-600 mt-2">Scan QR or use email check-in, then view your report anytime.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- UPCOMING EVENTS --}}
@if(isset($upcomingEvents) && $upcomingEvents->count())
<section id="events" class="py-14">
  <div class="text-center mb-8">
    <h2 class="text-2xl md:text-3xl font-extrabold">Upcoming Events</h2>
    <p class="mt-2 text-slate-600">
      Join meaningful events happening soon in your community.
    </p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    @foreach($upcomingEvents as $event)
      <div class="rounded-3xl bg-white/60 border border-white/60 shadow-sm overflow-hidden hover:shadow-md transition">

        {{-- Event poster --}}
        <div class="relative aspect-[3/4] w-full overflow-hidden bg-slate-100">
          @if($event->poster_path ?? false)
            <img
              src="{{ asset('storage/'.$event->poster_path) }}"
              alt="{{ $event->title }}"
              class="absolute inset-0 h-full w-full object-cover"
            />
          @else
            <div class="absolute inset-0 flex items-center justify-center
                        bg-gradient-to-br from-orange-100 to-pink-100
                        text-slate-500 text-sm font-semibold">
              No poster available
            </div>
          @endif
        </div>


        {{-- Event info --}}
        <div class="p-5 text-left space-y-2">
          <h3 class="text-lg font-extrabold text-slate-900 line-clamp-2">
            {{ $event->title }}
          </h3>

          <p class="text-sm text-slate-600">
            📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
          </p>

          <p class="text-sm text-slate-600">
            ⏰ {{ ucfirst($event->time_slot) }}
          </p>

          <p class="text-sm text-slate-600 line-clamp-1">
            📍 {{ $event->location }}
          </p>
        </div>

      </div>
    @endforeach
  </div>

</section>
@endif


  {{-- ABOUT / SERVICES / CONTACT --}}
  <section id="about" class="py-8">
    <div class="rounded-3xl bg-white/60 border border-white/60 p-8 shadow-sm">
      <h2 class="text-2xl md:text-3xl font-extrabold">About</h2>
      <p class="mt-3 text-slate-600 leading-relaxed">
        SmartVolunteer helps communities connect with meaningful volunteering opportunities and helps organizers run events smoothly.
      </p>
    </div>
  </section>

  <section id="services" class="py-6">
    <div class="rounded-3xl bg-white/60 border border-white/60 p-8 shadow-sm">
      <h2 class="text-2xl md:text-3xl font-extrabold">Services</h2>
      <p class="mt-3 text-slate-600 leading-relaxed">
        Event posting, volunteer signups, attendance tracking, and simple impact reporting — all built for speed and clarity.
      </p>
    </div>
  </section>

  <section id="contact" class="py-10">
    <div class="rounded-3xl bg-white/60 border border-white/60 p-8 shadow-sm">
      <h2 class="text-2xl md:text-3xl font-extrabold">Contact</h2>
      <p class="mt-3 text-slate-600 leading-relaxed">
        Questions or collaborations? Email
        <a class="font-semibold text-slate-900 underline decoration-orange-400" href="mailto:hello@smartvolunteer.test">
          hello@smartvolunteer.test
        </a>
      </p>
    </div>
  </section>

</div>
@endsection
