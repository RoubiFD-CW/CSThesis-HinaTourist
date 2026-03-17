<x-guest-layout>
    <div class="text-center">
        <!-- Error Code -->
        <h1
            class="text-9xl font-heading font-black text-transparent bg-clip-text bg-gradient-to-br from-cyan-400 to-teal-600 mb-2 drop-shadow-sm">
            404
        </h1>

        <!-- Icon -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-cyan-50 rounded-full flex items-center justify-center animate-bounce">
                <i class="fa-solid fa-map-location-dot text-4xl text-cyan-500"></i>
            </div>
        </div>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Page Not Found</h2>
        <p class="text-slate-500 mb-8 font-light">
            Sorry, we couldn't find the page you're looking for. <br>It might have been moved or doesn't exist.
        </p>

        <!-- Action -->
        <a href="/"
            class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-cyan-600 to-teal-600 text-white font-bold rounded-xl shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all duration-300">
            <i class="fa-solid fa-house"></i>
            Go Home
        </a>
    </div>
</x-guest-layout>