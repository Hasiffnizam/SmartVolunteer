@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] py-10 px-6">
  <div class="mx-auto max-w-3xl">
    <div class="rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-8">

      <div class="flex items-start justify-between gap-4">
        <div class="text-center flex-1">
          <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">
            Sign up for
            <span class="bg-gradient-to-r from-orange-500 to-pink-500 bg-clip-text text-transparent">
              SmartVolunteer
            </span>
          </h1>

          <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-orange-100 px-4 py-2
                      text-sm font-medium text-orange-700">
            Volunteer Registration
          </div>
        </div>

        <div class="w-12"></div>
      </div>

      <div class="mt-8 rounded-2xl bg-white/60 border border-white/60 p-6">
        <h2 class="text-xl font-bold text-slate-900 text-center">Volunteer Sign Up Form</h2>

        {{-- Stepper --}}
        <div class="mt-6 relative">
          <div class="absolute left-6 right-6 top-5 h-1 rounded-full bg-slate-200"></div>

          <div class="grid grid-cols-3 gap-2 relative">
            @php $labels = [1 => 'Personal', 2 => 'Login', 3 => 'Others']; @endphp

            @foreach([1,2,3] as $i)
              <div class="text-center">
                <div class="mx-auto h-10 w-10 rounded-full flex items-center justify-center font-bold
                  {{ $step >= $i ? 'text-white bg-gradient-to-r from-orange-500 to-pink-500 shadow-sm' : 'bg-slate-200 text-slate-600' }}">
                  {{ $i }}
                </div>

                <div class="mt-2 text-sm font-medium {{ $step >= $i ? 'text-slate-900' : 'text-slate-500' }}">
                  {{ $labels[$i] }}
                </div>
              </div>
            @endforeach
          </div>
        </div>

        @if ($errors->any())
          <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- STEP 1 --}}
        <form class="mt-6 {{ $step === 1 ? '' : 'hidden' }}"
              method="POST" action="{{ route('register.next') }}">
          @csrf
          <input type="hidden" name="step" value="1">

          <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="text-sm font-medium text-slate-700">Full Name</label>
              <input name="name" value="{{ old('name', $data['name'] ?? '') }}" required
                     class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            outline-none focus:ring-2 focus:ring-orange-300"
                     placeholder="Enter name">
            </div>

            <div>
              <label class="text-sm font-medium text-slate-700">Date of Birth</label>
              <input type="date" name="dob" value="{{ old('dob', $data['dob'] ?? '') }}"
                     class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            outline-none focus:ring-2 focus:ring-orange-300">
            </div>

            <div>
              <label class="text-sm font-medium text-slate-700">Contact</label>
              <input name="phone" value="{{ old('phone', $data['phone'] ?? '') }}"
                     class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            outline-none focus:ring-2 focus:ring-orange-300"
                     placeholder="e.g. 0123456789">
            </div>

            <div>
              <label class="text-sm font-medium text-slate-700">Gender</label>
              @php $g = old('gender', $data['gender'] ?? '') @endphp
              <select name="gender"
                      class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                             outline-none focus:ring-2 focus:ring-orange-300">
                <option value="">Select your gender</option>
                <option value="Male" {{ $g==='Male'?'selected':'' }}>Male</option>
                <option value="Female" {{ $g==='Female'?'selected':'' }}>Female</option>
                <option value="Other" {{ $g==='Other'?'selected':'' }}>Other</option>
              </select>
            </div>

            <div>
              <label class="text-sm font-medium text-slate-700">Occupation</label>
              <input name="occupation" value="{{ old('occupation', $data['occupation'] ?? '') }}"
                     class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            outline-none focus:ring-2 focus:ring-orange-300"
                     placeholder="e.g. Student">
            </div>

            <div class="md:col-span-2">
              <label class="text-sm font-medium text-slate-700">Address</label>
              <textarea name="address" rows="3"
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                               outline-none focus:ring-2 focus:ring-orange-300"
                        placeholder="Enter address">{{ old('address', $data['address'] ?? '') }}</textarea>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="rounded-2xl px-6 py-3 font-semibold text-white shadow-sm transition
                           bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
                           hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
              Next
            </button>
          </div>
        </form>

        {{-- STEP 2 --}}
        <form class="mt-6 {{ $step === 2 ? '' : 'hidden' }}"
              method="POST" action="{{ route('register.next') }}">
          @csrf
          <input type="hidden" name="step" value="2">

          <div class="space-y-4">
            <div>
              <label class="text-sm font-medium text-slate-700">Email</label>
              <input type="email" name="email" value="{{ old('email', $data['email'] ?? '') }}" required
                     class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                            outline-none focus:ring-2 focus:ring-orange-300"
                     placeholder="Enter email">
            </div>

            <div>
              <label class="text-sm font-medium text-slate-700">Password</label>
              <div class="relative">
                <input id="reg_password" type="password" name="password" required
                       class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 pr-12
                              outline-none focus:ring-2 focus:ring-orange-300"
                       placeholder="Minimum 8 characters">
                <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-900"
                        onclick="togglePass('reg_password', this)">👁</button>
              </div>
            </div>

            <div>
              <label class="text-sm font-medium text-slate-700">Confirm Password</label>
              <div class="relative">
                <input id="reg_password_confirmation" type="password" name="password_confirmation" required
                       class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 pr-12
                              outline-none focus:ring-2 focus:ring-orange-300"
                       placeholder="Repeat password">
                <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-900"
                        onclick="togglePass('reg_password_confirmation', this)">👁</button>
              </div>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-between">
            {{-- FIXED Previous: no nested form --}}
            <button type="submit"
                    formaction="{{ route('register.prev') }}"
                    formmethod="POST"
                    name="step"
                    value="2"
                    class="rounded-2xl px-6 py-3 font-semibold text-white shadow-sm transition
                           bg-slate-700 hover:bg-slate-800 hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
              @csrf
              Previous
            </button>

            <button type="submit"
                    class="rounded-2xl px-6 py-3 font-semibold text-white shadow-sm transition
                           bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
                           hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
              Next
            </button>
          </div>
        </form>

        {{-- STEP 3 --}}
        <form class="mt-6 {{ $step === 3 ? '' : 'hidden' }}"
              method="POST" action="{{ route('register.finish') }}">
          @csrf

          @php
            $savedSkills = old('skills', $data['skills'] ?? []);
            $savedSkills = array_map('intval', is_array($savedSkills) ? $savedSkills : []);

            $savedCauses = old('causes', $data['causes'] ?? []);
            $savedCauses = array_map('intval', is_array($savedCauses) ? $savedCauses : []);
          @endphp

          <div class="space-y-6">
            <div>
              <label class="text-sm font-semibold text-slate-700">Your Skills</label>

              <div class="mt-3 grid sm:grid-cols-2 gap-3">
                @foreach($skills as $s)
                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/80 px-4 py-3">
                    <input type="checkbox" name="skills[]" value="{{ $s->id }}"
                          {{ in_array((int)$s->id, $savedSkills, true) ? 'checked' : '' }}
                          class="rounded border-slate-300">
                    <span class="text-slate-800">{{ $s->name }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div>
              <label class="text-sm font-semibold text-slate-700">Causes for Which You Would Like To Volunteer</label>

              <div class="mt-3 grid sm:grid-cols-2 gap-3">
                @foreach($causes as $c)
                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/80 px-4 py-3">
                    <input type="checkbox" name="causes[]" value="{{ $c->id }}"
                          {{ in_array((int)$c->id, $savedCauses, true) ? 'checked' : '' }}
                          class="rounded border-slate-300">
                    <span class="text-slate-800">{{ $c->name }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-between">
            {{-- FIXED Previous: no nested form --}}
            <button type="submit"
                    formaction="{{ route('register.prev') }}"
                    formmethod="POST"
                    name="step"
                    value="3"
                    class="rounded-2xl px-6 py-3 font-semibold text-white shadow-sm transition
                           bg-slate-700 hover:bg-slate-800 hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
              @csrf
              Previous
            </button>

            <button type="submit"
                    class="rounded-2xl px-6 py-3 font-semibold text-white shadow-sm transition
                           bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
                           hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
              Sign Up
            </button>
          </div>
        </form>
      </div>

      <p class="mt-6 text-center text-sm text-slate-600">
        Already have an account?
        <a href="{{ route('login.show') }}" class="font-semibold text-slate-900 hover:underline">Login</a>
      </p>
    </div>
  </div>

  <script>
    function togglePass(id, btn) {
      const el = document.getElementById(id);
      if (!el) return;

      const isHidden = el.type === 'password';
      el.type = isHidden ? 'text' : 'password';

      if (btn) btn.textContent = isHidden ? '🙈' : '👁';
    }
  </script>
</section>
@endsection
