@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] flex items-center justify-center px-6 py-10">
  <div class="w-full max-w-md rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-lg p-8">

    <div class="text-center">
      <div class="mx-auto h-14 w-14 rounded-2xl bg-white/80 border border-white/60 flex items-center justify-center shadow-sm">
        <img
          src="{{ asset('images/smartvolunteer-logo.png') }}"
          alt="SmartVolunteer"
          class="h-10 w-10"
          onerror="this.style.display='none'"
        />
      </div>

      <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900">Forgot Password</h1>
      <p class="mt-2 text-sm text-slate-600">
        Enter your email and we’ll send you a password reset link.
      </p>
    </div>

    @if (session('status'))
      <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form class="mt-6 space-y-4" method="POST" action="{{ route('password.email') }}">
      @csrf

      <div>
        <label class="text-sm font-medium text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3
                      text-slate-900 outline-none focus:ring-2 focus:ring-orange-300"
               placeholder="Enter your email">
      </div>

      <button type="submit"
              class="w-full rounded-2xl py-3 font-semibold text-white shadow-sm transition
                     bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-400 hover:to-pink-400
                     hover:-translate-y-[1px] hover:shadow-md active:translate-y-0">
        Send Reset Link
      </button>

      <p class="text-center text-sm text-slate-600">
        Remember your password?
        <a href="{{ route('login.show') }}" class="font-semibold text-slate-900 hover:underline">
          Back to Login
        </a>
      </p>
    </form>
  </div>
</section>
@endsection
