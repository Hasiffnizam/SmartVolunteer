@extends('layouts.app')

@section('content')
<section class="min-h-[calc(100vh-120px)] px-6 py-8">
  <div class="mx-auto max-w-3xl rounded-3xl bg-white/70 border border-white/60 shadow-lg p-6">
    <h1 class="text-2xl font-bold text-slate-800">Event Check-in QR</h1>
    <p class="text-slate-600 mt-1">{{ $event->title }}</p>

    <div class="mt-6 grid gap-4">
      <div class="rounded-2xl bg-white border p-5">
        <div class="text-sm font-semibold text-slate-700 mb-3">Scan to Check-in</div>
        <div id="qrcode" class="grid place-items-center"></div>

        <div class="mt-4 text-xs text-slate-500 break-all">
          Link: {{ $signedUrl }}
        </div>
      </div>

      <a href="{{ route('admin.events.show', $event) }}"
         class="w-fit rounded-2xl px-4 py-2.5 font-semibold bg-white/70 border shadow-sm text-slate-700 hover:bg-white/90 transition">
        Back to Event
      </a>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
  new QRCode(document.getElementById("qrcode"), {
    text: @json($signedUrl),
    width: 240,
    height: 240
  });
</script>
@endsection
