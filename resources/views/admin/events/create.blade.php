@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-5xl">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Create New Event</h1>
        <p class="text-slate-600 mt-1">Fill in event details, upload a poster, and define role tasks.</p>
      </div>

      <a href="{{ route('admin.events.index') }}"
         class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Back
      </a>
    </div>

    {{-- Card --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6"
         x-data="eventCreateForm()"
         x-init="init()">

      <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ✅ Hidden JSON payload for role tasks --}}
        <input type="hidden" name="role_tasks_json" :value="roleTasksJson">

        {{-- Errors --}}
        @if ($errors->any())
          <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="font-bold mb-1">Please fix the following:</div>
            <ul class="list-disc pl-5 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Poster + Basic Info --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          {{-- Poster upload --}}
          <div class="lg:col-span-1">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Event Poster (optional)</label>

            <div class="rounded-3xl border border-slate-200 bg-white/60 p-4">
              <div class="aspect-[4/5] w-full rounded-2xl border border-slate-200 bg-white overflow-hidden flex items-center justify-center">
                <template x-if="posterPreview">
                  <img :src="posterPreview" alt="Poster preview" class="h-full w-full object-cover">
                </template>

                <template x-if="!posterPreview">
                  <div class="text-center text-slate-500 px-4">
                    <div class="text-3xl mb-2">🖼️</div>
                    <div class="font-semibold">Upload poster</div>
                    <div class="text-sm">JPG/PNG/WebP up to 4MB</div>
                  </div>
                </template>
              </div>

              <input
                type="file"
                name="poster"
                accept="image/*"
                class="mt-4 block w-full text-sm text-slate-600
                       file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-white file:font-semibold
                       hover:file:bg-slate-800"
                @change="onPosterChange($event)"
              />
            </div>
          </div>

          {{-- Details --}}
          <div class="lg:col-span-2 space-y-5">

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Event Title</label>
              <input type="text" name="title" value="{{ old('title') }}"
                     placeholder="e.g. Community Food Drive"
                     class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            text-slate-800 outline-none focus:ring-2 focus:ring-slate-300" />
            </div>

            <div class="mt-4">
              <label class="block text-sm font-semibold text-slate-700 mb-2">Event Description</label>
              <textarea
                name="description"
                rows="4"
                class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                      text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-slate-300"
                placeholder="Write a short description about what volunteers will do, agenda, requirements, etc."
              >{{ old('description') }}</textarea>
              <p class="mt-1 text-xs text-slate-500">Optional, but recommended.</p>
            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Date</label>
                <input type="date" name="event_date" value="{{ old('event_date') }}"
                       class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                              text-slate-800 outline-none focus:ring-2 focus:ring-slate-300" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Time Slot</label>
                @php $slotOld = old('time_slot', 'morning'); @endphp

                <div class="grid grid-cols-3 gap-2">
                  <label class="cursor-pointer">
                    <input type="radio" name="time_slot" value="morning" class="peer hidden" {{ $slotOld === 'morning' ? 'checked' : '' }}>
                    <div class="rounded-2xl border border-slate-200 bg-white/70 px-3 py-3 text-center font-semibold text-slate-700
                                hover:bg-white transition peer-checked:border-slate-900 peer-checked:bg-white">
                      Morning
                      <div class="text-xs font-normal text-slate-500 mt-0.5">8am–12pm</div>
                    </div>
                  </label>

                  <label class="cursor-pointer">
                    <input type="radio" name="time_slot" value="evening" class="peer hidden" {{ $slotOld === 'evening' ? 'checked' : '' }}>
                    <div class="rounded-2xl border border-slate-200 bg-white/70 px-3 py-3 text-center font-semibold text-slate-700
                                hover:bg-white transition peer-checked:border-slate-900 peer-checked:bg-white">
                      Evening
                      <div class="text-xs font-normal text-slate-500 mt-0.5">1pm–5pm</div>
                    </div>
                  </label>

                  <label class="cursor-pointer">
                    <input type="radio" name="time_slot" value="night" class="peer hidden" {{ $slotOld === 'night' ? 'checked' : '' }}>
                    <div class="rounded-2xl border border-slate-200 bg-white/70 px-3 py-3 text-center font-semibold text-slate-700
                                hover:bg-white transition peer-checked:border-slate-900 peer-checked:bg-white">
                      Night
                      <div class="text-xs font-normal text-slate-500 mt-0.5">6pm–10pm</div>
                    </div>
                  </label>
                </div>

              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Location</label>
              <input type="text" name="location" value="{{ old('location') }}"
                     placeholder="e.g. Dewan Komuniti Bangi"
                     class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            text-slate-800 outline-none focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Cause --}}
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Cause</label>
              <select name="cause_id"
                      class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                             text-slate-800 outline-none focus:ring-2 focus:ring-slate-300">
                @php $causeOld = old('cause_id'); @endphp
                <option value="" disabled {{ !$causeOld ? 'selected' : '' }}>Select a cause…</option>
                @foreach($causes as $c)
                  <option value="{{ $c->id }}" {{ (int)$causeOld === (int)$c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Skills --}}
            <div>
              <label class="block text-sm font-semibold text-slate-700">Skills Needed (optional)</label>

              <div class="mt-3 grid sm:grid-cols-2 gap-3">
                @foreach($skills as $s)
                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/80 px-4 py-3">
                    <input type="checkbox" name="skill_ids[]" value="{{ $s->id }}"
                           {{ in_array((int)$s->id, old('skill_ids', [])) ? 'checked' : '' }}
                           class="rounded border-slate-300">
                    <span class="text-slate-800">{{ $s->name }}</span>
                  </label>
                @endforeach
              </div>

              <p class="text-xs text-slate-500 mt-2">
                Pick from master skills (same list volunteers select during registration).
              </p>
            </div>

          </div>
        </div>

        {{-- Role Tasks --}}
        <div class="mt-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <h2 class="text-lg font-bold text-slate-800">Role Tasks</h2>
              <p class="text-slate-600 text-sm">Define tasks volunteers can sign up for (task name + number of slots).</p>
            </div>

            <button type="button"
                    @click="addTask()"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-2.5
                           bg-slate-900 text-white font-semibold shadow-sm hover:bg-slate-800 transition">
              <span class="text-lg leading-none">+</span>
              Add Task
            </button>
          </div>

          <div class="mt-4 space-y-3">
            <template x-for="(task, idx) in tasks" :key="task._key">
              <div class="rounded-3xl border border-slate-200 bg-white/60 p-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                  <div class="md:col-span-7">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Task Name</label>
                    <input type="text"
                           x-model="task.name"
                           @input="syncTasksJson()"
                           placeholder="e.g. Registration Counter"
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                                  text-slate-800 outline-none focus:ring-2 focus:ring-slate-300" />
                  </div>

                  <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Slots</label>
                    <input type="number" min="1" max="999"
                           x-model.number="task.slots"
                           @input="syncTasksJson()"
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                                  text-slate-800 outline-none focus:ring-2 focus:ring-slate-300" />
                  </div>

                  <div class="md:col-span-2 flex md:justify-end">
                    <button type="button"
                            @click="removeTask(idx)"
                            class="w-full md:w-auto rounded-2xl px-4 py-3 font-semibold
                                   border border-slate-200 bg-white hover:bg-slate-50 transition text-slate-700">
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </template>

            <template x-if="tasks.length === 0">
              <div class="rounded-3xl border border-slate-200 bg-white/60 p-8 text-center text-slate-600">
                No tasks added yet. Click <span class="font-semibold">Add Task</span> to create role tasks.
              </div>
            </template>
          </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:justify-end">
          <a href="{{ route('admin.events.index') }}"
             class="rounded-2xl px-5 py-3 font-semibold bg-white/70 border border-white/60 shadow-sm
                    text-slate-700 hover:bg-white/90 transition text-center">
            Cancel
          </a>

          <button type="submit"
                  class="rounded-2xl px-5 py-3 font-semibold bg-slate-900 text-white shadow-sm
                         hover:bg-slate-800 transition"
                  @click="syncTasksJson()">
            Create Event
          </button>
        </div>

      </form>
    </div>
  </div>
</section>

<script>
function eventCreateForm() {
  return {
    posterPreview: null,

    // ✅ This is what the UI uses
    tasks: [
      { _key: crypto.randomUUID(), name: 'Registration', slots: 3 },
      { _key: crypto.randomUUID(), name: 'Logistics & Setup', slots: 3 },
      { _key: crypto.randomUUID(), name: 'Crowd Control & Safety', slots: 3 },
    ],

    // ✅ This is what backend receives
    roleTasksJson: '[]',

    init() {
      this.syncTasksJson();
      this.$watch('tasks', () => this.syncTasksJson(), { deep: true });
    },

    onPosterChange(e) {
      const file = e.target.files?.[0];
      if (!file) {
        this.posterPreview = null;
        return;
      }
      this.posterPreview = URL.createObjectURL(file);
    },

    addTask() {
      this.tasks.push({ _key: crypto.randomUUID(), name: '', slots: 1 });
      this.syncTasksJson();
    },

    removeTask(i) {
      this.tasks.splice(i, 1);
      this.syncTasksJson();
    },

    syncTasksJson() {
      this.roleTasksJson = JSON.stringify(
        (this.tasks || []).map(t => ({
          name: (t.name || '').trim(),
          slots: parseInt(t.slots ?? 0, 10) || 0,
        }))
      );
    },
  };
}
</script>
@endsection
