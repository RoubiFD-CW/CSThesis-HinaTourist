<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PWA App') }} | Welcome</title>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Fade-up entrance animations */
        .splash-fade {
            animation: fadeUp 1s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .splash-fade-delay-1 {
            animation-delay: 0.2s;
        }

        .splash-fade-delay-2 {
            animation-delay: 0.5s;
        }

        .splash-fade-delay-3 {
            animation-delay: 0.8s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo glow pulse */
        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(99, 102, 241, 0.6);
            }
        }

        .glow {
            animation: pulse-glow 2.5s ease-in-out infinite;
        }

        /* Full-screen fade-out before redirect */
        .splash-exit {
            animation: fadeOut 0.6s ease-in forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: scale(1.02);
            }
        }

        /* Loading bar at bottom */
        .loading-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #a78bfa);
            border-radius: 0 2px 2px 0;
            animation: loadBar 3s ease-in-out forwards;
        }

        @keyframes loadBar {
            0% {
                width: 0%;
            }

            70% {
                width: 85%;
            }

            100% {
                width: 100%;
            }
        }
    </style>
</head>

<body class="antialiased bg-slate-950 text-white selection:bg-indigo-500 selection:text-white"
    style="background-color: #020617; background-image: radial-gradient(at center, #0e7490 0%, #0f172a 70%, #020617 100%);">

    <div id="splashContainer"
        class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

        <!-- Animated Background -->
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-[10%] left-[10%] w-72 h-72 rounded-full bg-cyan-400/20 blur-[120px]"></div>
            <div class="absolute bottom-[10%] right-[10%] w-80 h-80 rounded-full bg-teal-500/15 blur-[120px]"></div>
            <div
                class="absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-blue-600/10 blur-[150px]">
            </div>
        </div>

        <!-- Logo -->
        <div class="splash-fade mb-10">
            <div
                class="w-24 h-24 rounded-3xl bg-white flex items-center justify-center glow shadow-[0_0_40px_rgba(255,255,255,0.2)]">
                <img src="{{ asset('hinatourist-logo.png') }}" alt="Logo" class="w-16 h-16 object-contain">
            </div>
        </div>

        <!-- Title -->
        <h1
            class="splash-fade splash-fade-delay-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-center mb-4 text-white drop-shadow-lg">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-200 via-teal-200 to-blue-200"
                style="color: #67e8f9; -webkit-background-clip: text; background-clip: text;">
                {{ config('app.name', 'Hinatuan Tour') }}
            </span>
        </h1>

        <!-- Subtitle -->
        <p class="splash-fade splash-fade-delay-2 text-cyan-50/90 text-lg text-center mb-12 max-w-md drop-shadow-md font-medium"
            style="color: #cffafe;">
            A Web-Based Visitor Monitoring and Forecasting System for Hinatuan Tourism
        </p>

        <!-- Status Indicator -->
        <div class="splash-fade splash-fade-delay-3 flex items-center gap-2 text-sm text-cyan-200/90 font-medium">
            <svg class="w-4 h-4 animate-spin text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#22d3ee" stroke-width="4"></circle>
                <path class="opacity-75" fill="#22d3ee"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span id="statusText" style="color: #a5f3fc;">Loading...</span>
        </div>

        <!-- Dots indicator -->
        <div class="splash-fade splash-fade-delay-3 flex gap-2 mt-8">
            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
            <span class="w-2 h-2 rounded-full bg-white/20"></span>
            <span class="w-2 h-2 rounded-full bg-white/20"></span>
        </div>
    </div>

    <!-- Loading bar -->
    <div class="loading-bar"></div>

    <script>
        /**
         * Splash Screen Auto-Redirect
         * Waits for the splash animations to play, detects viewport,
         * then redirects: desktop → /home, mobile → /login
         */
        (async function splashRedirect() {
            const MOBILE_BREAKPOINT = 640; // Tailwind 'sm' breakpoint in px
            const SPLASH_DISPLAY_MS = 3000; // Show splash for 3 seconds
            const FADE_OUT_MS = 600; // Match the fadeOut animation duration

            // Helper: wait for a given number of milliseconds
            const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));

            // Step 1: Let the splash animations play
            await wait(SPLASH_DISPLAY_MS);

            // Step 2: Detect if the user is on mobile or desktop
            const isMobile = window.innerWidth < MOBILE_BREAKPOINT;
            const destination = isMobile ? '/login' : '/home';

            // Update status text
            const statusEl = document.getElementById('statusText');
            statusEl.textContent = isMobile ? 'Redirecting to Sign In...' : 'Redirecting to Home...';

            // Step 3: Brief pause to show the status message
            await wait(500);

            // Step 4: Fade out the splash screen
            const container = document.getElementById('splashContainer');
            container.classList.add('splash-exit');

            // Step 5: Wait for the fade-out animation, then navigate
            await wait(FADE_OUT_MS);
            window.location.href = destination;
        })();
    </script>
</body>

</html>