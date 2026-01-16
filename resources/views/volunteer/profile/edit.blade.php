@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-4xl">

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Edit Profile</h1>
        <p class="text-slate-600 mt-1">Update your details and profile picture.</p>
      </div>

      <a href="{{ route('volunteer.profile.show') }}"
         class="rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border border-white/60 shadow-sm
                text-slate-700 hover:bg-white/90 transition">
        Back
      </a>
    </div>

    <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
      <form method="POST" action="{{ route('volunteer.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Avatar --}}
        <div>
          <label class="block text-sm font-semibold text-slate-700">Profile Picture</label>
          <div class="mt-2 flex items-center gap-4">
            <div class="h-16 w-16 rounded-2xl bg-white/80 border border-white/60 overflow-hidden flex items-center justify-center">
              @if($user->avatar_path)
                <img src="{{ asset('storage/'.$user->avatar_path) }}" class="h-full w-full object-cover" alt="Avatar">
              @else
                <span class="text-slate-400 text-xs font-semibold">No Photo</span>
              @endif
            </div>

            <input type="file" name="avatar"
                   class="block w-full text-sm text-slate-700
                          file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                          file:bg-white/80 file:text-slate-700 hover:file:bg-white"
                   accept="image/*">
          </div>
          @error('avatar')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {{-- Name --}}
          <div>
            <label class="block text-sm font-semibold text-slate-700">Full Name</label>
            <input name="name" value="{{ old('name', $user->name) }}"
                   class="mt-2 w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3
                          focus:outline-none focus:ring-2 focus:ring-slate-200">
            @error('name')
              <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Email --}}
          <div>
            <label class="block text-sm font-semibold text-slate-700">Email</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}"
                   class="mt-2 w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3
                          focus:outline-none focus:ring-2 focus:ring-slate-200">
            @error('email')
              <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Phone --}}
          <div>
            <label class="block text-sm font-semibold text-slate-700">Phone</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}"
                   class="mt-2 w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3
                          focus:outline-none focus:ring-2 focus:ring-slate-200">
            @error('phone')
              <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Role (read-only) --}}
          <div>
            <label class="block text-sm font-semibold text-slate-700">Role</label>
            <input value="{{ ucfirst($user->role ?? 'volunteer') }}" disabled
                   class="mt-2 w-full rounded-2xl bg-slate-100 border border-white/60 px-4 py-3 text-slate-600">
          </div>

          {{-- Address --}}
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-700">Address</label>
            <textarea name="address" rows="4"
                      class="mt-2 w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3
                             focus:outline-none focus:ring-2 focus:ring-slate-200">{{ old('address', $user->address) }}</textarea>
            @error('address')
              <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Skills --}}
        <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
          <div class="text-sm font-semibold text-slate-700">My Skills</div>
          <p class="text-xs text-slate-500 mt-1">Select skills you have.</p>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($skills as $s)
              @php
                $checked = in_array($s->id, old('skill_ids', $selectedSkillIds ?? []));
              @endphp

              <label class="flex items-center gap-3 rounded-2xl bg-white/70 border border-white/60 px-4 py-3 hover:bg-white/90 transition">
                <input
                  type="checkbox"
                  name="skill_ids[]"
                  value="{{ $s->id }}"
                  @checked($checked)
                  class="h-4 w-4 rounded border-slate-300 text-slate-800 focus:ring-slate-200"
                >
                <span class="text-sm text-slate-700 font-medium">{{ $s->name }}</span>
              </label>
            @endforeach
          </div>

          @error('skill_ids')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
          @error('skill_ids.*')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        {{-- Causes --}}
        <div class="mt-6 rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-6">
          <div class="text-sm font-semibold text-slate-700">Preferred Causes</div>
          <p class="text-xs text-slate-500 mt-1">Pick causes you care about.</p>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($causes as $c)
              @php
                $checked = in_array($c->id, old('cause_ids', $selectedCauseIds ?? []));
              @endphp

              <label class="flex items-center gap-3 rounded-2xl bg-white/70 border border-white/60 px-4 py-3 hover:bg-white/90 transition">
                <input
                  type="checkbox"
                  name="cause_ids[]"
                  value="{{ $c->id }}"
                  @checked($checked)
                  class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-200"
                >
                <span class="text-sm text-slate-700 font-medium">{{ $c->name }}</span>
              </label>
            @endforeach
          </div>

          @error('cause_ids')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
          @error('cause_ids.*')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

          {{-- Change Password --}}
          <div class="mt-6 pt-5 border-t border-white/60"
              x-data="{ showCurrent:false, showNew:false, showConfirm:false }">

            <h3 class="text-base font-bold text-slate-800">Change Password</h3>
            <p class="text-sm text-slate-600 mt-1">Leave blank if you don’t want to change it.</p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

              {{-- Current Password --}}
              <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700">Current Password</label>

                <div class="mt-2 relative">
                  <input
                    :type="showCurrent ? 'text' : 'password'"
                    name="current_password"
                    placeholder="Enter current password"
                    class="w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3 pr-12
                          focus:outline-none focus:ring-2 focus:ring-slate-200"
                  />

                  <button type="button"
                          @click="showCurrent = !showCurrent"
                          class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">
                    {{-- Eye --}}
                    <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                              c4.477 0 8.268 2.943 9.542 7
                              -1.274 4.057-5.065 7-9.542 7
                              -4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>

                    {{-- Eye Off --}}
                    <svg x-show="showCurrent" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.875 18.825A10.05 10.05 0 0112 19
                              c-4.478 0-8.269-2.943-9.543-7
                              a9.973 9.973 0 011.563-3.029"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.18 6.18A9.956 9.956 0 0112 5
                              c4.477 0 8.268 2.943 9.542 7
                              a9.97 9.97 0 01-4.043 5.153"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3l18 18"/>
                    </svg>
                  </button>
                </div>

                @error('current_password')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- New Password --}}
              <div>
                <label class="block text-sm font-semibold text-slate-700">New Password</label>

                <div class="mt-2 relative">
                  <input
                    :type="showNew ? 'text' : 'password'"
                    name="new_password"
                    placeholder="Minimum 8 characters"
                    class="w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3 pr-12
                          focus:outline-none focus:ring-2 focus:ring-slate-200"
                  />

                  <button type="button"
                          @click="showNew = !showNew"
                          class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">
                    {{-- Eye --}}
                    <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                              c4.477 0 8.268 2.943 9.542 7
                              -1.274 4.057-5.065 7-9.542 7
                              -4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>

                    {{-- Eye Off --}}
                    <svg x-show="showNew" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.875 18.825A10.05 10.05 0 0112 19
                              c-4.478 0-8.269-2.943-9.543-7"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3l18 18"/>
                    </svg>
                  </button>
                </div>

                @error('new_password')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- Confirm New Password --}}
              <div>
                <label class="block text-sm font-semibold text-slate-700">Confirm New Password</label>

                <div class="mt-2 relative">
                  <input
                    :type="showConfirm ? 'text' : 'password'"
                    name="new_password_confirmation"
                    placeholder="Repeat new password"
                    class="w-full rounded-2xl bg-white/80 border border-white/60 px-4 py-3 pr-12
                          focus:outline-none focus:ring-2 focus:ring-slate-200"
                  />

                  <button type="button"
                          @click="showConfirm = !showConfirm"
                          class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">
                    {{-- Eye --}}
                    <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                              c4.477 0 8.268 2.943 9.542 7
                              -1.274 4.057-5.065 7-9.542 7
                              -4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>

                    {{-- Eye Off --}}
                    <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.875 18.825A10.05 10.05 0 0112 19
                              c-4.478 0-8.269-2.943-9.543-7"/>
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3l18 18"/>
                    </svg>
                  </button>
                </div>
              </div>

            </div>
          </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <a href="{{ route('volunteer.profile.show') }}"
             class="rounded-2xl px-4 py-3 font-semibold bg-white/70 border border-white/60 shadow-sm
                    text-slate-700 hover:bg-white/90 transition">
            Cancel
          </a>

          <button type="submit"
                  class="rounded-2xl px-5 py-3 font-semibold text-white shadow-sm
                         bg-slate-800 hover:bg-slate-900 transition">
            Save Changes
          </button>
        </div>

      </form>
    </div>

  </div>
</section>
@endsection
