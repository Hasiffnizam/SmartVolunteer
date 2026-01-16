@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-4xl">

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">My Profile</h1>
        <p class="text-slate-600 mt-1">View your profile details.</p>
      </div>

      <a href="{{ route('volunteer.profile.edit') }}"
         class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Edit Profile
      </a>
    </div>

    {{-- Profile completeness --}}
    <div class="mt-4 rounded-3xl bg-white/60 backdrop-blur border border-white/60 shadow-sm p-5">
      <div class="flex items-center justify-between gap-4">
        <div>
          <div class="text-sm font-semibold text-slate-700">Profile completeness</div>
          <div class="text-xs text-slate-500 mt-1">
            {{ $profileCompletionPercent }}% complete
            @if(!empty($profileMissingFields))
              • Missing: {{ implode(', ', $profileMissingFields) }}
            @endif
          </div>
        </div>

        <div class="text-sm font-bold text-slate-800">
          {{ $profileCompletionPercent }}%
        </div>
      </div>

      <div class="mt-3 h-2.5 w-full rounded-full bg-slate-200/60 overflow-hidden">

      @php
        $p = max(0, min(100, (int) $profileCompletionPercent));
        $widthClass = match (true) {
          $p >= 100 => 'w-full',
          $p >= 95  => 'w-[95%]',
          $p >= 90  => 'w-[90%]',
          $p >= 85  => 'w-[85%]',
          $p >= 80  => 'w-[80%]',
          $p >= 75  => 'w-[75%]',
          $p >= 70  => 'w-[70%]',
          $p >= 65  => 'w-[65%]',
          $p >= 60  => 'w-[60%]',
          $p >= 55  => 'w-[55%]',
          $p >= 50  => 'w-[50%]',
          $p >= 45  => 'w-[45%]',
          $p >= 40  => 'w-[40%]',
          $p >= 35  => 'w-[35%]',
          $p >= 30  => 'w-[30%]',
          $p >= 25  => 'w-[25%]',
          $p >= 20  => 'w-[20%]',
          $p >= 15  => 'w-[15%]',
          $p >= 10  => 'w-[10%]',
          $p >= 5   => 'w-[5%]',
          default   => 'w-0',
        };
      @endphp

      <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-pink-500 transition-all duration-300 {{ $widthClass }}"></div>

    </div>


    @if(session('success'))
      <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('success') }}
      </div>
    @endif

    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
      <div class="flex items-center gap-5">
        <div class="h-20 w-20 rounded-2xl bg-white/80 border border-white/60 overflow-hidden flex items-center justify-center">
          @if($user->avatar_path)
            <img src="{{ asset('storage/'.$user->avatar_path) }}" class="h-full w-full object-cover" alt="Avatar">
          @else
            <span class="text-slate-400 font-semibold">No Photo</span>
          @endif
        </div>

        <div>
          <div class="text-xl font-bold text-slate-800">{{ $user->name }}</div>
          <div class="text-slate-600">{{ $user->email }}</div>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-white/60 border border-white/60 p-4">
          <div class="text-xs font-semibold text-slate-500 uppercase">Phone</div>
          <div class="mt-1 text-slate-800">{{ $user->phone ?? '—' }}</div>
        </div>

      <div class="rounded-2xl bg-white/60 border border-white/60 p-4">
        <div class="text-xs font-semibold text-slate-500 uppercase">Date of Birth</div>
        <div class="mt-1 text-slate-800">
          @if(!empty($user->dob))
            {{ \Carbon\Carbon::parse($user->dob)->format('d M Y') }}
          @else
            —
          @endif
        </div>
      </div>

        <div class="rounded-2xl bg-white/60 border border-white/60 p-4">
          <div class="text-xs font-semibold text-slate-500 uppercase">Role</div>
          <div class="mt-1 text-slate-800">{{ ucfirst($user->role ?? 'volunteer') }}</div>
        </div>

        <div class="sm:col-span-2 rounded-2xl bg-white/60 border border-white/60 p-4">
          <div class="text-xs font-semibold text-slate-500 uppercase">Address</div>
          <div class="mt-1 text-slate-800 whitespace-pre-line">{{ $user->address ?? '—' }}</div>
        </div>

        <div class="text-sm font-semibold text-slate-700">My Skills</div>
        <div class="mt-2 flex flex-wrap gap-2">
          @forelse($user->skills()->get() as $s)
            <span class="rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 border border-slate-200 text-slate-700">
              {{ $s->name }}
            </span>
          @empty
            <span class="text-slate-500">—</span>
          @endforelse
        </div>

        <div class="mt-5 text-sm font-semibold text-slate-700">Preferred Causes</div>
        <div class="mt-2 flex flex-wrap gap-2">
          @forelse($user->causes()->get() as $c)
            <span class="rounded-full px-3 py-1 text-xs font-semibold bg-orange-50 border border-orange-200 text-orange-700">
              {{ $c->name }}
            </span>
          @empty
            <span class="text-slate-500">—</span>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</section>
@endsection
