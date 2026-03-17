<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-slate-800">Welcome back</h2>
        <p class="text-sm text-slate-500">Sign in to your account</p>
    </div>

    {{-- Session Errors --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div x-data="loginHandler">
        <form @submit.prevent="submitForm($event)" action="/login" method="POST">
            @csrf

            <!-- Email -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white/50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-400 transition-all duration-200"
                    placeholder="attendant@example.com">
            </div>

            <!-- Password -->
            <div class="mb-5 relative" x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <input :type="show ? 'text' : 'password'" name="password" id="password" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white/50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-400 transition-all duration-200 pr-12"
                    placeholder="••••••••">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-[38px] text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>

            <!-- Forgot Password -->
            <div class="flex justify-end mb-6">
                <a href="/forgot-password"
                    class="text-sm text-cyan-600 hover:text-cyan-700 font-medium transition-colors">
                    Forgot password?
                </a>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full py-3.5 px-6 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.97]">
                Sign In
            </button>
        </form>
    </div>

    <!-- Create Account -->
    <!-- <p class="mt-6 text-center text-sm text-slate-500">
        Don't have an account?
        <a href="/register" class="text-cyan-600 hover:text-cyan-700 font-semibold transition-colors">
            Create account
        </a>
    </p> -->
</x-guest-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loginHandler', () => ({
            submitForm(event) {
                if (!navigator.onLine) {
                    this.$dispatch('toast', 'No internet connection. Please check your network.');
                    return;
                }

                // Submit the form if online
                event.target.submit();
            }
        }));
    });
</script>