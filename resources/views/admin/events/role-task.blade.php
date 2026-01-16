@extends('layouts.app')

@php
  $roleTasksForJs = $event->roleTasks->map(function ($t) {
    return [
      'id' => $t->id,
      'uid' => 'task_' . $t->id,
      'title' => $t->title,
      'slots' => (int) $t->slots,
      'slots_taken' => (int) ($t->slots_taken ?? 0),
    ];
  })->values();
@endphp

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-6xl" x-data="roleTaskAdminPage(window.__ROLE_TASKS__)">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Role Tasks</h1>
        <div class="mt-2 inline-flex items-center gap-2 rounded-2xl bg-white/70 backdrop-blur border border-white/60 px-4 py-2 text-sm text-slate-700">
          <span class="font-semibold">{{ $event->title }}</span>
          <span class="text-slate-400">•</span>
          <span>{{ $event->location }}</span>
          <span class="text-slate-400">•</span>
          <span>{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</span>
          <span class="text-slate-400">•</span>
          <span class="capitalize">{{ $event->time_slot }}</span>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.events.show', $event->id) }}"
           class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                  text-slate-700 hover:bg-white/90 transition">
          Back
        </a>

        <button type="button"
                class="rounded-2xl px-4 py-2.5 font-semibold bg-slate-900 text-white shadow-sm
                       hover:bg-slate-800 transition"
                @click="addTask()">
          + Add Task
        </button>
      </div>
    </div>

    @if(session('success'))
      <div class="mt-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 font-semibold">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mt-5 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800">
        <div class="font-bold mb-2">Please fix:</div>
        <ul class="list-disc ml-5 space-y-1">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.events.role-task.update', $event->id) }}" class="mt-6">
      @csrf
      @method('PUT')

      <input type="hidden" name="role_tasks_json" :value="jsonPayload()" />

      <div class="space-y-6">
        <template x-for="(t, idx) in tasks" :key="t.uid">
          <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Task Name</label>
                <input
                  type="text"
                  class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                         text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-slate-300"
                  placeholder="e.g., Registration / Crowd Control / First Aid"
                  x-model="t.title"
                />
              </div>

              <div class="w-full md:w-56">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Slots</label>
                <input
                  type="number"
                  min="1"
                  class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                         text-slate-800 outline-none focus:ring-2 focus:ring-slate-300"
                  x-model.number="t.slots"
                />
              </div>

              <div class="w-full md:w-32 md:pt-8">
                <button type="button"
                        class="w-full rounded-2xl px-4 py-3 font-semibold bg-white border border-slate-200
                               text-slate-700 hover:bg-slate-50 transition"
                        @click="removeTask(idx)">
                  Remove
                </button>
              </div>
            </div>

            {{-- Filled display (read-only) --}}
            <div class="mt-5 flex items-center justify-between">
              <div class="text-sm text-slate-600">
                Filled:
                <span class="font-semibold text-slate-800" x-text="t.slots_taken"></span>
                <span class="text-slate-400">/</span>
                <span class="font-semibold text-slate-800" x-text="t.slots"></span>
              </div>

              <span class="rounded-full px-3 py-1 text-xs font-semibold border"
                    :class="(t.slots - t.slots_taken) <= 0
                      ? 'bg-slate-200 text-slate-700 border-slate-300'
                      : 'bg-emerald-100 text-emerald-800 border-emerald-200'">
                <span x-text="Math.max(0, t.slots - t.slots_taken)"></span> slot(s) left
              </span>
            </div>

            {{-- Progress bar --}}
            <div class="mt-3 h-2 rounded-full bg-slate-200 overflow-hidden">
              <div class="h-full bg-slate-900" :style="`width: ${progressPct(t)}%`"></div>
            </div>

            <p class="mt-3 text-xs text-slate-500">
              Note: Filled slots update automatically when volunteers join (attendance uses the same registrations).
            </p>
          </div>
        </template>

        <template x-if="tasks.length === 0">
          <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-10 text-center">
            <div class="mx-auto h-14 w-14 rounded-2xl bg-white border border-slate-200 grid place-items-center">🧩</div>
            <h3 class="mt-4 text-lg font-bold text-slate-800">No role tasks yet</h3>
            <p class="mt-1 text-slate-600">Click “Add Task” to create tasks for volunteers.</p>
          </div>
        </template>
      </div>

      <div class="mt-8 flex justify-end">
        <button type="submit"
                class="rounded-2xl px-8 py-3 font-semibold bg-slate-900 text-white shadow-sm
                       hover:bg-slate-800 transition">
          Save
        </button>
      </div>
    </form>

  </div>
</section>


<script type="application/json" id="roleTasksJson">
{!! json_encode($roleTasksForJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>

<script>
  window.__ROLE_TASKS__ = JSON.parse(
    document.getElementById('roleTasksJson')?.textContent || '[]'
  );
</script>



<script>
  function roleTaskAdminPage(initialTasks = []) {
    return {
      tasks: Array.isArray(initialTasks) ? initialTasks : [],

      addTask() {
        this.tasks.push({
          id: null,
          uid: 'new_' + Math.random().toString(16).slice(2),
          title: '',
          slots: 1,
          slots_taken: 0,
        });
      },

      removeTask(idx) {
        this.tasks.splice(idx, 1);
      },

      progressPct(t) {
        const total = Math.max(1, Number(t.slots || 1));
        const taken = Math.max(0, Number(t.slots_taken || 0));
        return Math.min(100, Math.round((taken / total) * 100));
      },

      jsonPayload() {
        return JSON.stringify(this.tasks.map(t => ({
          id: t.id,
          name: (t.title || '').trim(),
          slots: Number(t.slots || 1),
        })));
      }
    }
  }
</script>
@endsection
