@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl" x-data="attendancePage()">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Attendance</h1>
        <p class="text-slate-600 mt-1">
          Mark volunteers who attended this event. Tick to mark <span class="font-semibold">Present</span>.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.events.show', $event->id) }}"
           class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                  text-slate-700 hover:bg-white/90 transition">
          Back
        </a>

        <button type="submit"
                form="attendanceForm"
                class="rounded-2xl px-4 py-2.5 font-semibold bg-slate-900 text-white shadow-sm
                       hover:bg-slate-800 transition">
          Save
        </button>
      </div>
    </div>

    @if(session('success'))
      <div class="mt-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 font-semibold">
        {{ session('success') }}
      </div>
    @endif

    {{-- Summary + Search --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                       bg-slate-100 text-slate-700 border border-slate-200">
            Present: <span class="ml-1" x-text="presentCount()"></span>
          </span>

          <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                       bg-slate-100 text-slate-700 border border-slate-200">
            Total: <span class="ml-1" x-text="filteredRows().length"></span>
          </span>

          <button type="button"
                  class="rounded-xl px-3 py-2 text-sm font-semibold border border-slate-200 bg-white hover:bg-slate-50 transition"
                  @click="markAll(true)">
            Mark all present
          </button>

          <button type="button"
                  class="rounded-xl px-3 py-2 text-sm font-semibold border border-slate-200 bg-white hover:bg-slate-50 transition"
                  @click="markAll(false)">
            Clear all
          </button>
        </div>

        <div class="relative w-full sm:w-96">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
          <input
            type="text"
            x-model="q"
            placeholder="Search volunteer name…"
            class="w-full rounded-2xl border border-slate-200 bg-white/80 pl-11 pr-4 py-3
                   text-slate-800 placeholder:text-slate-400 outline-none
                   focus:ring-2 focus:ring-slate-300"
          />
        </div>
      </div>
    </div>

    {{-- FORM: Real DB save --}}
    <form id="attendanceForm" method="POST" action="{{ route('admin.events.attendance.save', $event) }}">
      @csrf

      {{-- Grouped by Role Task --}}
      <div class="mt-6 space-y-6">
        @forelse($byTask as $roleTaskId => $regs)
          @php $task = optional($regs->first())->roleTask; @endphp

          <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-white/60 bg-white/40">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                  <h2 class="text-lg font-bold text-slate-800">
                    {{ $task?->title ?? 'Role Task' }}
                  </h2>
                  @if($task?->description)
                    <p class="text-sm text-slate-600 mt-1">{{ $task->description }}</p>
                  @endif
                </div>

                @if($task)
                  @php
                    $total = (int)($task->slots ?? 0);
                    $taken = (int)($task->slots_taken ?? 0);
                    $avail = max(0, $total - $taken);
                  @endphp
                  <div class="flex gap-2">
                    <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                      Slots: {{ $total }}
                    </span>
                    <span class="rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                      Taken: {{ $taken }}
                    </span>
                    <span class="rounded-2xl px-3 py-1 text-xs font-semibold
                                 {{ $avail === 0 ? 'bg-slate-200 text-slate-700' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                      Available: {{ $avail }}
                    </span>
                  </div>
                @endif
              </div>
            </div>

            <div class="p-6 space-y-4">
              @foreach($regs as $reg)
                @php
                  $v = $reg->volunteer;
                  $name = $v?->name ?? 'Volunteer';
                  $email = $v?->email ?? '';
                  $initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                    ->take(2)
                    ->join('');
                @endphp

                <div class="attendance-row rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-sm overflow-hidden"
                     x-show="matches('{{ e($name) }}', '{{ e($email) }}')">
                  <div class="p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                      {{-- Left: Volunteer info --}}
                      <div class="flex items-center gap-4 min-w-0">
                        <div class="h-12 w-12 rounded-2xl bg-white/80 border border-slate-200 grid place-items-center text-slate-600">
                          {{ $initials }}
                        </div>

                        <div class="min-w-0">
                          <div class="font-bold text-slate-800 truncate">{{ $name }}</div>
                          <div class="text-sm text-slate-500 truncate">
                            <span>{{ $email }}</span>
                            <span class="mx-2">•</span>
                            <span>Volunteer</span>
                          </div>
                        </div>
                      </div>

                      {{-- Right: Status + Toggle --}}
                      <div class="flex items-center justify-between md:justify-end gap-3">
                        <span
                          class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold border"
                          :class="isChecked('{{ $reg->id }}')
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                            : 'bg-slate-100 text-slate-700 border-slate-200'"
                          x-text="isChecked('{{ $reg->id }}') ? 'Present' : 'Absent'"
                        ></span>

                        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                          <span class="text-sm font-semibold text-slate-700">Attend</span>

                          {{-- Toggle checkbox (REAL FORM FIELD) --}}
                          <input
                            type="checkbox"
                            class="hidden"
                            name="attendance[{{ $reg->id }}][present]"
                            value="1"
                            x-ref="cb{{ $reg->id }}"
                            @change="sync()"
                            {{ $reg->present ? 'checked' : '' }}
                          >

                          <span
                            class="w-12 h-7 rounded-full border transition relative"
                            :class="isChecked('{{ $reg->id }}') ? 'bg-emerald-500 border-emerald-500' : 'bg-white border-slate-300'"
                          >
                            <span
                              class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow transition"
                              :class="isChecked('{{ $reg->id }}') ? 'translate-x-5' : ''"
                            ></span>
                          </span>
                        </label>
                      </div>
                    </div>

                    {{-- Note --}}
                    <div class="mt-4">
                      <label class="block text-xs font-semibold text-slate-500 mb-1">Note (optional)</label>
                      <input
                        type="text"
                        name="attendance[{{ $reg->id }}][note]"
                        value="{{ old('attendance.'.$reg->id.'.note', $reg->note) }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                               text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-slate-300"
                        placeholder="e.g., arrived late / left early"
                      />
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

        @empty
          <div class="rounded-3xl border border-slate-200 bg-white/60 p-10 text-center">
            <div class="mx-auto h-14 w-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center">
              👥
            </div>
            <h3 class="mt-4 text-lg font-bold text-slate-800">No volunteers yet</h3>
            <p class="mt-1 text-slate-600">No one has joined this event.</p>
          </div>
        @endforelse
      </div>
    </form>

  </div>
</section>

<script>
  function attendancePage() {
    return {
      q: '',
      // cache checkboxes status (for instant present count without heavy DOM queries)
      checked: {},

      init() {
        this.sync();
      },

      sync() {
        // rebuild checked map from DOM refs (fast enough for typical attendance sizes)
        this.checked = {};
        document.querySelectorAll('#attendanceForm input[type="checkbox"][name$="[present]"]').forEach(cb => {
          // reg id from name: attendance[123][present]
          const m = cb.name.match(/^attendance\[(\d+)\]\[present\]$/);
          if (m) this.checked[m[1]] = cb.checked;
        });
      },

      isChecked(regId) {
        return !!this.checked[String(regId)];
      },

      matches(name, email) {
        const k = (this.q || '').trim().toLowerCase();
        if (!k) return true;
        return (name || '').toLowerCase().includes(k) || (email || '').toLowerCase().includes(k);
      },

      filteredRows() {
        // approximate count: count visible rows by keyword (same logic as matches)
        const k = (this.q || '').trim().toLowerCase();
        const rows = [];
        document.querySelectorAll('.attendance-row').forEach(el => {
          if (!k) { rows.push(el); return; }
          const txt = (el.innerText || '').toLowerCase();
          if (txt.includes(k)) rows.push(el);
        });
        return rows;
      },

      presentCount() {
        // count checked among visible rows
        const rows = this.filteredRows();
        const ids = [];
        rows.forEach(el => {
          const cb = el.querySelector('input[type="checkbox"][name$="[present]"]');
          if (!cb) return;
          const m = cb.name.match(/^attendance\[(\d+)\]\[present\]$/);
          if (m) ids.push(m[1]);
        });

        return ids.filter(id => this.checked[id]).length;
      },

      markAll(val) {
        document.querySelectorAll('.attendance-row').forEach(el => {
          // only mark visible under current search
          if (el.style.display === 'none') return;
          const cb = el.querySelector('input[type="checkbox"][name$="[present]"]');
          if (cb) cb.checked = val;
        });
        this.sync();
      },
    }
  }

  document.addEventListener('alpine:init', () => {
    // ensure init runs after Alpine loads
  });
</script>
@endsection
