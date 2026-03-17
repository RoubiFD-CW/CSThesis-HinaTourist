<x-guest-layout>
    <div class="text-center">
        <!-- Error Code -->
        <h1
            class="text-6xl font-heading font-black text-transparent bg-clip-text bg-gradient-to-br from-amber-400 to-orange-600 mb-6 drop-shadow-sm">
            Session Expired
        </h1>

        <!-- Icon -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center animate-pulse">
                <i class="fa-solid fa-hourglass-end text-4xl text-amber-500"></i>
            </div>
        </div>

        <!-- Message -->
        <p class="text-slate-500 mb-8 font-light">
            Your session has timed out due to inactivity. <br>Please refresh the page to continue.
        </p>

        <!-- Action -->
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40 hover:-translate-y-0.5 transition-all duration-300">
            <i class="fa-solid fa-rotate-right"></i>
            Refresh Page
        </button>
    </div>
</x-guest-layout>