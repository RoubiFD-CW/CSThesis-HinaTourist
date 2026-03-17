<x-guest-layout>
    <div class="text-center">
        <!-- Icon -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-cyan-50 rounded-full flex items-center justify-center animate-pulse">
                <i class="fa-solid fa-screwdriver-wrench text-4xl text-cyan-500"></i>
            </div>
        </div>

        <!-- Message -->
        <h1 class="text-3xl font-heading font-black text-slate-900 mb-3">Under Maintenance</h1>
        <p class="text-slate-500 mb-8 font-light">
            We're currently performing scheduled maintenance to improve our services.
            <br>We'll be back online shortly.
        </p>

        <!-- Spinner -->
        <div class="flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-500"></div>
        </div>
    </div>
</x-guest-layout>