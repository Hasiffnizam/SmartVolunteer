@php
  $user = auth()->user();
  $isAdmin = $user && ($user->role === 'admin');

  // Keep dropdown open if currently in any admin.events.* route
  $r = request()->route() ? request()->route()->getName() : '';
  $isEventSection = str_starts_with($r, 'admin.events.');

  // Selected event id (route model binding OR query string)
  $selectedEventId = request()->route('event')?->id ?? request('event');

  // Active state for global report analysis
  $isReportAnalysisActive = ($r === 'admin.report_analysis.index');
@endphp

<aside
  :class="sidebarCollapsed ? 'w-16' : 'w-72'"
  class="h-[calc(100vh-120px)] transition-all duration-300"
>
  <div class="h-full rounded-2xl bg-white/60 backdrop-blur border border-white/60 shadow-sm p-3 overflow-y-auto">

    <nav class="space-y-2">

      @if($isAdmin)
        {{-- ================= ADMIN ================= --}}
        <div x-data="{ open: {{ $isEventSection ? 'true' : 'true' }} }">

          {{-- Event Management toggle --}}
          <button
            type="button"
            @click="open = !open"
            class="w-full flex items-center justify-between px-3 py-2 rounded-xl font-semibold text-slate-700
                   hover:bg-white/70 border border-transparent hover:border-slate-200 transition"
            :title="sidebarCollapsed ? 'Event Management' : ''"
          >
            <span class="flex items-center gap-3">
              <span class="h-10 w-10 grid place-items-center rounded-xl bg-white/70 border border-slate-200">📅</span>
              <span x-show="!sidebarCollapsed" x-transition>Event Management</span>
            </span>

            <svg
              x-show="!sidebarCollapsed"
              class="h-4 w-4 text-slate-500 transition"
              :class="open ? 'rotate-180' : ''"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                clip-rule="evenodd"/>
            </svg>
          </button>

          {{-- Event Management dropdown --}}
          <div x-show="open && !sidebarCollapsed" x-transition class="pl-14 space-y-1" style="display:none;">

            {{-- Event Details --}}
            <a
              href="{{ $selectedEventId ? route('admin.events.show', $selectedEventId) : route('admin.events.index') }}"
              class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-white/70"
            >
              Event Details
              @if(!$selectedEventId)
                <span class="ml-2 text-xs text-slate-400">(select event)</span>
              @endif
            </a>

            {{-- Role Task --}}
            <a
              href="{{ $selectedEventId ? route('admin.events.role-task', $selectedEventId) : route('admin.events.index') }}"
              class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-white/70"
            >
              Role Task
              @if(!$selectedEventId)
                <span class="ml-2 text-xs text-slate-400">(select event)</span>
              @endif
            </a>

            {{-- Attendance --}}
            <a
              href="{{ $selectedEventId
                        ? route('admin.events.attendance', ['event' => $selectedEventId])
                        : route('admin.events.index') }}"
              class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-white/70"
            >
              Attendance
              @if(!$selectedEventId)
                <span class="ml-2 text-xs text-slate-400">(select event)</span>
              @endif
            </a>

            {{-- Report (Selected Event) --}}
            <a
              href="{{ $selectedEventId ? route('admin.events.report', $selectedEventId) : route('admin.events.index') }}"
              class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-white/70"
            >
              Report
              @if(!$selectedEventId)
                <span class="ml-2 text-xs text-slate-400">(select event)</span>
              @endif
            </a>

          </div>
        </div>

        {{-- ================= GLOBAL REPORT ANALYSIS ================= --}}
        <a
          href="{{ route('admin.report_analysis.index') }}"
          class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold transition
                 border {{ $isReportAnalysisActive
                   ? 'bg-white/80 border-slate-200 text-slate-900 shadow-sm'
                   : 'border-transparent text-slate-700 hover:bg-white/70 hover:border-slate-200'
                 }}"
          :title="sidebarCollapsed ? 'Report Analysis' : ''"
        >
          <span class="h-10 w-10 grid place-items-center rounded-xl bg-white/70 border border-slate-200">📊</span>
          <span x-show="!sidebarCollapsed" x-transition>Report Analysis</span>
        </a>

      @else
        {{-- ================= VOLUNTEER ================= --}}
        @php
          $isExploreActive  = str_starts_with($r, 'volunteer.explore.');
          $isMyEventsActive = str_starts_with($r, 'volunteer.myevents.');
          $isMyReportActive = ($r === 'volunteer.report');
        @endphp

        <a href="{{ route('volunteer.explore.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold transition
                  border {{ $isExploreActive ? 'bg-white/80 border-slate-200 text-slate-900 shadow-sm' : 'border-transparent text-slate-700 hover:bg-white/70 hover:border-slate-200' }}">
          <span class="h-10 w-10 grid place-items-center rounded-xl bg-white/70 border border-slate-200">🧭</span>
          <span x-show="!sidebarCollapsed" x-transition>Explore Event</span>
        </a>

        <a href="{{ route('volunteer.myevents.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold transition
                  border {{ $isMyEventsActive ? 'bg-white/80 border-slate-200 text-slate-900 shadow-sm' : 'border-transparent text-slate-700 hover:bg-white/70 hover:border-slate-200' }}">
          <span class="h-10 w-10 grid place-items-center rounded-xl bg-white/70 border border-slate-200">⭐</span>
          <span x-show="!sidebarCollapsed" x-transition>My Event</span>
        </a>

        <a href="{{ route('volunteer.report') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold transition
                  border {{ $isMyReportActive ? 'bg-white/80 border-slate-200 text-slate-900 shadow-sm' : 'border-transparent text-slate-700 hover:bg-white/70 hover:border-slate-200' }}">
          <span class="h-10 w-10 grid place-items-center rounded-xl bg-white/70 border border-slate-200">📄</span>
          <span x-show="!sidebarCollapsed" x-transition>My Report</span>
        </a>
      @endif

    </nav>

  </div>
</aside>
