<x-guest-layout>
    <!-- Back to Home -->
    <a href="/home"
        class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-slate-600 transition-colors group mb-5">
        <i class="fa-solid fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-0.5"></i>
        Back to Home
    </a>

    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Welcome back</h2>
        <p class="text-sm text-slate-400 mt-1">Log In to your account</p>
    </div>

    {{-- Session Errors --}}
    @if($errors->any())
        <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600 flex items-start gap-2.5">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-400 shrink-0"></i>
            <div>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div x-data="loginHandler">
        <form @submit.prevent="submitForm($event)" action="/login" method="POST">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-600 mb-1.5">Email Address</label>
                <div class="relative">
                    <i
                        class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                        placeholder="Enter your email">
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4" x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-slate-600 mb-1.5">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input :type="show ? 'text' : 'password'" name="password" id="password" required
                        class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                        placeholder="Enter your password">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                        <i class="fa-regular text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Forgot Password -->
            <div class="flex justify-end mb-5">
                <a href="/forgot-password"
                    class="text-xs text-[#008080] hover:text-[#006666] font-medium transition-colors">
                    Forgot password?
                </a>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full py-2.5 px-5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98]">
                Log In
            </button>
        </form>
    </div>
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