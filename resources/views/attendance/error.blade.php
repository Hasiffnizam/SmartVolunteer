@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-10">
  <div class="mx-auto max-w-xl rounded-3xl bg-white/70 border border-white/60 shadow-lg p-6 text-center">
    <div class="text-4xl">⚠️</div>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Cannot Check-in</h1>
    <p class="text-slate-600 mt-2">{{ $message }}</p>
  </div>
</section>
@endsection
