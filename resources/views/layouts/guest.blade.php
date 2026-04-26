<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HinaTourist') }} | Authentication</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#008080">
    <link rel="icon" type="image/png" href="{{ asset('hinatourist-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        [x-cloak] { display: none !important; }

        .guest-bg {
            background-color: #f8fafc;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(22, 125, 119, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(79, 195, 247, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(26, 75, 159, 0.04) 0%, transparent 50%);
        }

        /* Unified focus style */
        input:focus {
            border-color: #008080 !important;
            box-shadow: 0 0 0 3px rgba(22, 125, 119, 0.15) !important;
            outline: none !important;
        }

        /* Fade-in animation */
        @keyframes authFadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .auth-card { animation: authFadeIn 0.5s ease-out forwards; }
    </style>
</head>

<body x-data="{ showToast: false, toastMessage: '' }"
    @toast.window="toastMessage = $event.detail; showToast = true; setTimeout(() => showToast = false, 3000)"
    class="guest-bg font-sans text-slate-900 antialiased min-h-screen flex flex-col items-center justify-center px-4 py-8 sm:py-12">

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] bg-rose-500 text-white px-5 py-2.5 rounded-full shadow-xl flex items-center gap-2.5 whitespace-nowrap ring-1 ring-white/10"
        style="display: none;" x-cloak>
        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
        <span x-text="toastMessage" class="font-medium text-sm"></span>
    </div>

    <!-- Card Container -->
    <div class="w-full max-w-[420px] relative z-10">

        <!-- Auth Card -->
        <div class="auth-card bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-slate-100/80 overflow-hidden">
            <div class="px-6 py-7 sm:px-8 sm:py-8">
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