<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hinatuan Tour') }} | Authentication</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#22d3ee">
    <link rel="icon" type="image/png" href="{{ asset('hinatourist-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ showToast: false, toastMessage: '' }"
    @toast.window="toastMessage = $event.detail; showToast = true; setTimeout(() => showToast = false, 3000)"
    class="font-sans text-slate-900 antialiased min-h-screen flex items-center justify-center p-4 bg-white">

    <!-- Toast Notification (Global) -->
    <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[100] bg-rose-500 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 whitespace-nowrap shadow-rose-500/20 ring-1 ring-white/10"
        style="display: none;" x-cloak>
        <i class="fa-solid fa-wifi-slash text-white"></i>
        <span x-text="toastMessage" class="font-medium text-sm"></span>
    </div>

    <!-- Simple Container -->
    <div class="w-full max-w-md relative z-10">
        <div class="bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden animate-fade-up">
            <div class="p-6 sm:p-8">
                <!-- Back to Home Button -->
                <div class="mb-6 hidden md:block">
                    <a href="/"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-slate-500 transition-all bg-slate-50 rounded-full hover:bg-slate-100 hover:text-slate-800 group">
                        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                        <span>Back to Home</span>
                    </a>
                </div>

                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </div>
    </div>



</body>

</html>