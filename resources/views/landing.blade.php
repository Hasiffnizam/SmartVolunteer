@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

  <section class="min-h-[85vh] flex items-center justify-center">
    <div class="text-center max-w-3xl">
      <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/70 border border-white/60 text-xs font-semibold text-slate-700 shadow-sm">
        ✨ One platform for volunteering
      </p>

      <h1 class="mt-6 text-4xl md:text-6xl font-extrabold tracking-tight leading-tight">
        Volunteer smarter,
        <span class="bg-gradient-to-r from-orange-500 to-pink-500 bg-clip-text text-transparent">
          impact bigger.
        </span>
      </h1>

      <p class="mt-5 text-base md:text-lg text-slate-600 leading-relaxed">
        Find opportunities, manage events, and track contributions — all in one simple place.
      </p>

      <div class="mt-8 flex items-center justify-center">
        <a href="{{ route('register.show') }}"
           class="px-7 py-3 rounded-2xl text-white font-semibold shadow-sm transition
                  bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
                  hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
          Get Started
        </a>
      </div>
    </div>
  </section>

  <section id="about" class="py-14">
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

  <section id="contact" class="py-14">
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
