@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-5xl">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Edit Event</h1>
        <p class="text-slate-600 mt-1">Update event details, poster, and settings.</p>
      </div>

      <a href="{{ route('admin.events.index') }}"
         class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Back
      </a>
    </div>

    {{-- Card --}}
    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6"
         x-data="eventEditForm(
           '{{ $event->poster_path ? asset('storage/'.$event->poster_path) : '' }}',
           @js($event->tasks->map(fn($t)=>[
             'id' => $t->id,
             'name' => $t->name,
             'slots_total' => $t->slots_total,
           ])->values())
         )"
         x-init="init()">

      {{-- Errors --}}
      @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
          <div class="font-semibold mb-2">Please fix the errors below:</div>
          <ul class="list-disc pl-5 space-y-1 text-sm">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST"
            action="{{ route('admin.events.update', $event->id) }}"
            enctype="multipart/form-data"
            class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Top grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

          {{-- Poster --}}
          <div class="md:col-span-1">
            <div class="rounded-3xl border border-slate-200 bg-white/60 p-4">
              <div class="text-sm font-semibold text-slate-700 mb-3">Poster</div>

              <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <template x-if="previewUrl && previewUrl.length">
                  <img :src="previewUrl" class="w-full h-56 object-cover" alt="Poster preview">
                </template>

                <template x-if="!previewUrl || !previewUrl.length">
                  <div class="h-56 grid place-items-center text-slate-400">
                    🖼️ No Poster
                  </div>
                </template>
              </div>

              <div class="mt-3">
                <input type="file"
                       name="poster"
                       accept="image/*"
                       class="block w-full text-sm text-slate-600
                              file:mr-3 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2
                              file:text-white file:font-semibold hover:file:bg-slate-800"
                       @change="onFileChange($event)" />

                @error('poster')
                  <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror

                <p class="mt-2 text-xs text-slate-500">Upload to replace the existing poster.</p>
              </div>
            </div>
          </div>

          {{-- Main fields --}}
          <div class="md:col-span-2 space-y-5">

            {{-- Title --}}
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Event Title</label>
              <input type="text"
                     name="title"
                     value="{{ old('title', $event->title) }}"
                     class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            text-slate-800 placeholder:text-slate-400 outline-none
                            focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., Kempen Derma Darah" />
              @error('title')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Title description --}}
            <div class="mt-4">
              <label class="block text-sm font-semibold text-slate-700 mb-2">Event Description</label>
              <textarea
                name="description"
                rows="4"
                class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                      text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-slate-300"
                placeholder="Write a short description about what volunteers will do, agenda, requirements, etc."
              >{{ old('description', $event->description) }}</textarea>
            </div>

            {{-- Location --}}
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Location</label>
              <input type="text"
                     name="location"
                     value="{{ old('location', $event->location) }}"
                     class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            text-slate-800 placeholder:text-slate-400 outline-none
                            focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., Dewan Latihan Intan" />
              @error('location')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Cause + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Cause</label>
                @php
                  $causeVal = old('cause_id', $event->cause_id);
                @endphp
                <select name="cause_id"
                        class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                               text-slate-800 outline-none focus:ring-2 focus:ring-slate-300">
                  <option value="" disabled {{ !$causeVal ? 'selected' : '' }}>Select a cause…</option>
                  @foreach($causes as $c)
                    <option value="{{ $c->id }}" @selected((int)$causeVal === (int)$c->id)>
                      {{ $c->name }}
                    </option>
                  @endforeach
                </select>
                @error('cause_id')
                  <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="status"
                        class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                               text-slate-800 outline-none focus:ring-2 focus:ring-slate-300">
                  @php $statusVal = old('status', $event->status); @endphp
                  <option value="Upcoming"     @selected($statusVal === 'Upcoming')>Upcoming</option>
                  <option value="Ongoing" @selected($statusVal === 'Ongoing')>Ongoing</option>
                  <option value="Completed"    @selected($statusVal === 'Completed')>Completed</option>
                </select>
                @error('status')
                  <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- Date + Slot --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Event Date</label>
                <input type="date"
                       name="event_date"
                       value="{{ old('event_date', optional($event->event_date)->format('Y-m-d')) }}"
                       class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                              text-slate-800 outline-none focus:ring-2 focus:ring-slate-300" />
                @error('event_date')
                  <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Time Slot</label>
                <select name="time_slot"
                        class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                               text-slate-800 outline-none focus:ring-2 focus:ring-slate-300">
                  @php $slotVal = old('time_slot', $event->time_slot); @endphp
                  <option value="morning" @selected($slotVal === 'morning')>Morning</option>
                  <option value="evening" @selected($slotVal === 'evening')>Evening</option>
                  <option value="night"   @selected($slotVal === 'night')>Night</option>
                </select>
                @error('time_slot')
                  <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- Skills --}}
            <div>
              <label class="block text-sm font-semibold text-slate-700">Skills Needed (optional)</label>

              @php
                $selectedSkillIds = old('skill_ids', $event->skills->pluck('id')->map(fn($v)=>(int)$v)->toArray());
              @endphp

              <div class="mt-3 grid sm:grid-cols-2 gap-3">
                @foreach($skills as $s)
                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/80 px-4 py-3">
                    <input type="checkbox"
                           name="skill_ids[]"
                           value="{{ $s->id }}"
                           @checked(in_array((int)$s->id, $selectedSkillIds))
                           class="rounded border-slate-300">
                    <span class="text-slate-800">{{ $s->name }}</span>
                  </label>
                @endforeach
              </div>

              @error('skill_ids')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
              @error('skill_ids.*')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror

              <p class="text-xs text-slate-500 mt-2">
                Select skills that volunteers should have for this event.
              </p>
            </div>

          </div>
        </div>

        {{-- Footer actions --}}
        <div class="flex items-center justify-end gap-2 pt-2">
          <a href="{{ route('admin.events.show', $event->id) }}"
             class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                    text-slate-700 hover:bg-white/90 transition">
            Cancel
          </a>

          <button type="submit"
                  class="rounded-2xl px-5 py-2.5 font-semibold bg-slate-900 text-white shadow-sm
                         hover:bg-slate-800 transition">
            Save Changes
          </button>
        </div>

      </form>
    </div>

  </div>
</section>

<script>
function eventEditForm(initialPosterUrl, initialTasks) {
  return {
    previewUrl: initialPosterUrl || '',

    tasks: (initialTasks || []).map(t => ({
      id: t.id,
      name: t.name || '',
      slots_total: Number(t.slots_total || 1),
    })),

    init() {
      // nothing required, but keeps pattern consistent
    },

    get tasksJson() {
      // controller expects "name" + "slots" (or can handle slots_total too)
      return JSON.stringify(
        (this.tasks || []).map(t => ({
          id: t.id,
          name: (t.name || '').trim(),
          slots: parseInt(t.slots_total ?? 0, 10) || 0,
        }))
      );
    },

    onFileChange(e) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      this.previewUrl = URL.createObjectURL(file);
    },
  }
}
</script>
@endsection
