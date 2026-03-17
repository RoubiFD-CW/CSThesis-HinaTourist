<x-guest-layout>
    <div class="text-center">
        <!-- Error Code -->
        <h1
            class="text-9xl font-heading font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-400 to-violet-600 mb-2 drop-shadow-sm">
            500
        </h1>

        <!-- Icon -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center animate-spin-slow">
                <i class="fa-solid fa-gears text-4xl text-indigo-500"></i>
            </div>
        </div>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Something went wrong</h2>
        <p class="text-slate-500 mb-8 font-light">
            It's not you, it's us. We experienced an internal server error. <br>Please try refreshing the page or come
            back later.
        </p>

        <!-- Action -->
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-300">
            <i class="fa-solid fa-rotate-right"></i>
            Refresh Page
        </button>
    </div>
</x-guest-layout>