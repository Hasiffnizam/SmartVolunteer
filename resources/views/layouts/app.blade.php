<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartVolunteer</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style> html { scroll-behavior: smooth; } </style>

  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to   { opacity: 1; transform: scale(1); }
    }
    .animate-fadeIn {
      animation: fadeIn 0.25s ease-out;
    }
  </style>
</head>

<body
  x-data="{
    sidebarCollapsed: (localStorage.getItem('sv_sidebar_collapsed') ?? '0') === '1',
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
      localStorage.setItem(
        'sv_sidebar_collapsed',
        this.sidebarCollapsed ? '1' : '0'
      );
    }
  }"
  @sidebar-toggle.window="toggleSidebar()"
  class="min-h-screen w-full bg-gradient-to-br from-orange-50 via-white to-pink-50 text-slate-800"
>

  {{-- Soft background blobs --}}
  <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-orange-200/40 blur-3xl"></div>
    <div class="absolute top-40 -right-28 h-80 w-80 rounded-full bg-pink-200/40 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-amber-200/30 blur-3xl"></div>
  </div>

  {{-- Navbar --}}
  <x-navbar />

@php
  // Sidebar should appear for logged-in users
  $showSidebar = auth()->check();
@endphp

{{-- Sidebar --}}
@if($showSidebar)
  <div class="fixed left-0 top-0 h-screen z-40 pt-24 px-4">
    @hasSection('sidebar')
      @yield('sidebar')
    @else
      @include('components.sidebar.shell')
    @endif
  </div>
@endif

{{-- Main content --}}
<main
  class="transition-all duration-300 pt-6 px-6"
  :class="
    {{ $showSidebar ? 'sidebarCollapsed ? \'pl-20\' : \'pl-80\'' : '\'\' ' }}
  "
>
  <div class="max-w-6xl mx-auto">
    @yield('content')
  </div>

  <footer class="pb-10 text-center text-sm text-slate-500 mt-10">
    © {{ date('Y') }} SmartVolunteer. All rights reserved.
  </footer>
</main>

{{-- ✅ GLOBAL SUCCESS POPUP (NO BLADE INSIDE JS FILES ANYMORE) --}}
@if (session('success'))
  <div id="sv-success" data-message="{{ e(session('success')) }}"></div>
@endif

<script>
  (function () {
    const el = document.getElementById('sv-success');
    if (!el) return;

    const msg = el.dataset.message || '';
    if (!msg) return;

    if (window.Swal) {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: msg,
        confirmButtonColor: '#0f172a',
        timer: 2200,
        timerProgressBar: true
      });
    }
  })();
</script>

</body>
</html>
