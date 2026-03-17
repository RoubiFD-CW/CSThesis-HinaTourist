<x-guest-layout>
    <div class="text-center">
        <!-- Error Code -->
        <h1
            class="text-9xl font-heading font-black text-transparent bg-clip-text bg-gradient-to-br from-rose-400 to-red-600 mb-2 drop-shadow-sm">
            403
        </h1>

        <!-- Icon -->
        <div class="mb-6 flex justify-center">
            <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center animate-bounce">
                <i class="fa-solid fa-ban text-4xl text-rose-500"></i>
            </div>
        </div>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Access Denied</h2>
        <p class="text-slate-500 mb-8 font-light">
            You don't have permission to access this page. <br>Contact an administrator if you believe this is an error.
        </p>

        <!-- Action -->
        <a href="{{ Auth::user() && Auth::user()->is_admin ? '/admin/dashboard' : (Auth::user() ? '/user/dashboard' : '/') }}"
            class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 hover:-translate-y-0.5 transition-all duration-300">
            <i class="fa-solid fa-arrow-left"></i>
            Return to Dashboard
        </a>
    </div>
</x-guest-layout>