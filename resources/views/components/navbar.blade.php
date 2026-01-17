<header class="w-full">
  <nav class="w-full rounded-2xl bg-white/60 backdrop-blur border border-white/60 shadow-sm z-50 relative">
    <div class="w-full px-5 py-3 flex items-center justify-between">

      {{-- LEFT: Sidebar toggle + Brand --}}
      <div class="flex items-center gap-3">

        {{-- Sidebar toggle (only logged in users) --}}
        @auth
          <button
            type="button"
            @click="$dispatch('sidebar-toggle')"
            class="h-10 w-10 grid place-items-center rounded-xl
                   bg-white/70 hover:bg-white
                   border border-white/60 shadow-sm
                   transition"
            title="Toggle sidebar"
          >
            <svg class="h-5 w-5 text-slate-700"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
              <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
        @endauth

        {{-- Brand --}}
        <a href="{{ url('/') }}" class="flex items-center gap-3">
          <img
            src="{{ asset('images/smartvolunteer-logo.png') }}"
            alt="SmartVolunteer"
            class="h-10 w-10"
            onerror="this.style.display='none'"
          />
          <span class="font-extrabold tracking-tight text-lg
                       bg-gradient-to-r from-orange-500 via-orange-400 to-pink-500
                       bg-clip-text text-transparent">
            SmartVolunteer
          </span>
        </a>
      </div>

      {{-- RIGHT: Desktop --}}
      <div class="hidden md:flex items-center gap-5 text-sm font-semibold text-slate-700">

        {{-- ================= GUEST ================= --}}
        @guest
          <a href="{{ url('/#about') }}"
             class="relative hover:text-slate-900 transition
                    after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0
                    after:bg-gradient-to-r after:from-orange-500 after:to-pink-500
                    hover:after:w-full after:transition-all after:duration-300">
            About
          </a>

          <a href="{{ url('/#features') }}"
             class="relative hover:text-slate-900 transition
                    after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0
                    after:bg-gradient-to-r after:from-orange-500 after:to-pink-500
                    hover:after:w-full after:transition-all after:duration-300">
            How it works
          </a>

          <a href="{{ url('/#contact') }}"
             class="relative hover:text-slate-900 transition
                    after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0
                    after:bg-gradient-to-r after:from-orange-500 after:to-pink-500
                    hover:after:w-full after:transition-all after:duration-300">
            Contact
          </a>

          <span class="mx-1 h-5 w-px bg-slate-300/80"></span>

          <a href="{{ route('login.show') }}"
             class="px-4 py-2 rounded-xl bg-white/70 hover:bg-white
                    border border-slate-200 text-sm font-semibold transition">
            Login
          </a>

          <a href="{{ route('register.show') }}"
             class="px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-md transition
                    bg-gradient-to-r from-orange-500 to-pink-500
                    hover:from-orange-400 hover:to-pink-400
                    hover:shadow-lg">
            Join as Volunteer
          </a>
        @endguest

        {{-- ================= AUTH ================= --}}
        @auth
          @php
            $user = auth()->user();
            $isAdmin = ($user->role === 'admin');
          @endphp

          <span class="text-base font-semibold text-slate-700">
            {{ $isAdmin ? 'Welcome Admin' : 'Welcome - '.$user->name }}
          </span>

          {{-- Avatar dropdown --}}
          <div x-data="{ open:false }" class="relative">
            <button
              type="button"
              @click="open = !open"
              @click.outside="open = false"
              class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-300"
            >
              @php
                $avatarUrl = $isAdmin
                  ? asset('images/smartvolunteer-logo.png')
                  : ($user->avatar_path
                      ? asset('storage/'.$user->avatar_path)
                      : asset('images/default-avatar.png'));
              @endphp

              <img
                class="h-10 w-10 rounded-full object-cover border border-white/70 shadow-sm"
                src="{{ $avatarUrl }}"
                alt="Profile"
                onerror="this.src='{{ asset('images/smartvolunteer-logo.png') }}'"
              />

              <svg class="h-5 w-5 text-slate-500"
                   viewBox="0 0 20 20"
                   fill="currentColor">
                <path fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                      clip-rule="evenodd"/>
              </svg>
            </button>

            <div x-show="open" x-transition
                 class="absolute right-0 mt-3 w-56 rounded-2xl bg-white shadow-xl border border-slate-100 overflow-hidden"
                 style="display:none;">
              <div class="px-4 py-3 border-b border-slate-100">
                <div class="text-sm font-semibold text-slate-800">{{ $user->name }}</div>
                <div class="text-xs text-slate-500">{{ $user->email }}</div>
              </div>

              {{-- Volunteer profile --}}
              @if(!$isAdmin)
                <a href="{{ route('volunteer.profile.show') }}"
                   class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                  Profile
                </a>
              @endif

              <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                  Logout
                </button>
              </form>
            </div>
          </div>
        @endauth
      </div>

      {{-- MOBILE --}}
      <div class="md:hidden flex items-center gap-2">
        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm font-semibold text-red-600">Logout</button>
          </form>
        @else
          <a href="{{ route('register.show') }}"
             class="px-4 py-2 rounded-xl text-white text-sm font-bold
                    bg-gradient-to-r from-orange-500 to-pink-500">
            Join
          </a>
        @endauth
      </div>

    </div>
  </nav>
</header>
