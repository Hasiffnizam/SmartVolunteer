@extends('layouts.app')

@section('content')

@if (session('password_reset_success'))
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
  <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-xl text-center">
    <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-gradient-to-r from-orange-500 to-pink-500
                flex items-center justify-center text-white text-2xl">
      ✓
    </div>

    <h2 class="text-xl font-bold text-slate-900">Password Reset Successful</h2>

    <p class="mt-2 text-sm text-slate-600">
      {{ session('password_reset_success') }}
    </p>

    <a href="{{ route('login.show') }}"
       class="mt-6 inline-block w-full rounded-2xl py-3 font-semibold text-white
              bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
              transition">
      Continue to Login
    </a>
  </div>
</div>
@endif

<section class="min-h-[calc(100vh-120px)] flex items-center justify-center px-6 py-10">
  <div class="w-full max-w-md rounded-3xl bg-white/80 backdrop-blur border border-white/60 shadow-lg p-8">

    <div class="flex flex-col items-center text-center">
      <div class="h-14 w-14 rounded-2xl bg-white/80 border border-white/60 shadow-sm flex items-center justify-center">
        <img src="{{ asset('images/smartvolunteer-logo.png') }}" alt="SmartVolunteer" class="h-10 w-10"
             onerror="this.style.display='none'" />
      </div>

      <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900">Welcome Back</h1>
      <p class="mt-2 text-slate-600 text-sm">Continue your journey of making a difference</p>
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

    <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
      @csrf

      <div>
        <label class="text-sm font-medium text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                      outline-none focus:ring-2 focus:ring-orange-300">
      </div>

      {{-- Password with show/hide --}}
      <div x-data="{ show:false }">
        <label class="text-sm font-medium text-slate-700">Password</label>

        <div class="mt-1 relative">
          <input
            :type="show ? 'text' : 'password'"
            name="password"
            required
            autocomplete="current-password"
            class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 pr-12
                   outline-none focus:ring-2 focus:ring-orange-300"
            placeholder="Enter your password"
          />

          <button type="button"
                  @click="show = !show"
                  class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">

            {{-- Eye --}}
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg"
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
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg"
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

      <div class="flex items-center justify-between text-sm text-slate-600">
        <label class="flex items-center gap-2">
          <input type="checkbox" name="remember" class="rounded border-slate-300">
          Remember me
        </label>

        <a href="{{ route('password.request') }}"
           class="text-slate-500 hover:text-slate-900 hover:underline">
          Forgot password?
        </a>
      </div>

      <button type="submit"
        class="w-full rounded-2xl py-3 font-semibold text-white shadow-sm transition
               bg-gradient-to-r from-orange-500 to-pink-500
               hover:from-orange-400 hover:to-pink-400
               hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
        Login
      </button>

      <p class="text-center text-sm text-slate-600">
        Don’t have an account?
        <a href="{{ route('register.show') }}" class="font-semibold text-slate-900 hover:underline">
          Sign up
        </a>
      </p>
    </form>
  </div>
</section>
@endsection
