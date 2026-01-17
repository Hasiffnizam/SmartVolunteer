@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-10">
  <div class="mx-auto max-w-xl rounded-3xl bg-white/70 border border-white/60 shadow-lg p-6 text-center">
    <div class="text-3xl">📍</div>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Confirm Check-in</h1>
    <p class="text-slate-600 mt-1">{{ $event->title }}</p>

    <form method="POST" action="{{ route('checkin.qr.confirm', $event) }}" class="mt-6">
      @csrf
      <button type="submit"
        class="w-full rounded-2xl px-5 py-3 font-semibold text-white shadow-lg bg-gradient-to-r from-pink-500 to-orange-500 hover:opacity-95 transition">
        Confirm Check-in
      </button>
    </form>

    <p class="text-xs text-slate-500 mt-4">
      If you already checked in, you’ll see a success message.
    </p>
  </div>
</section>
@endsection
