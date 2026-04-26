<x-guest-layout>
    <!-- Back to Home -->
    <a href="/home" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-slate-600 transition-colors group mb-5">
        <i class="fa-solid fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-0.5"></i>
        Back to Home
    </a>

    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-envelope-circle-check text-xl"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Verify Your Email</h2>
        <p class="text-sm text-slate-400 mt-1.5 leading-relaxed max-w-xs mx-auto">
            Please check your inbox and click the verification link to activate your account.
        </p>
    </div>

    @if (session('message'))
        <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-600 font-medium text-center flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col gap-2.5">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full py-2.5 px-5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98]">
                <i class="fa-solid fa-paper-plane mr-1.5"></i>
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full py-2.5 px-5 bg-slate-50 text-slate-500 text-sm font-medium rounded-xl border border-slate-200 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-right-from-bracket mr-1.5"></i>
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
