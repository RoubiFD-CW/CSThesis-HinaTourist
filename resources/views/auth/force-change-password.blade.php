<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-shield-halved text-amber-500 text-xl"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Update Password Required</h2>
        <p class="text-sm text-slate-500 mt-1">For your security, please change your default password to continue.</p>
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

    <div x-data="forceChangeHandler">
        <form @submit.prevent="submitForm($event)" action="{{ route('password.force-update') }}" method="POST">
            @csrf

            <!-- New Password -->
            <div class="mb-4" x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-slate-600 mb-1.5">New Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input :type="show ? 'text' : 'password'" name="password" id="password" required autofocus
                        oninput="checkResetConstraints()"
                        class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                        placeholder="Enter new password">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                        <i class="fa-regular text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6" x-data="{ show: false }">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-600 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                        oninput="checkResetConstraints()"
                        class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                        placeholder="Confirm new password">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                        <i class="fa-regular text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Password Constraints -->
            <div class="mb-4 p-3 bg-slate-50 rounded-xl text-xs space-y-1.5 border border-slate-100">
                <p id="rc-upper" class="text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-[5px]"></i> At least one uppercase letter
                </p>
                <p id="rc-number" class="text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-[5px]"></i> At least one number
                </p>
                <p id="rc-special" class="text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-[5px]"></i> At least one special character
                </p>
                <p id="rc-length" class="text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-[5px]"></i> Minimum 8 characters
                </p>
            </div>
            <p id="matchError" class="hidden mb-5 text-xs text-rose-500 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Passwords do not match
            </p>

            <!-- Submit -->
            <button type="submit" id="resetBtn" disabled
                class="w-full py-2.5 px-5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:shadow-none">
                Update Password
            </button>
        </form>

        <!-- Logout fallback -->
        <div class="mt-6 text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-rose-500 font-medium transition-colors">
                    Logout instead
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('forceChangeHandler', () => ({
            submitForm(event) {
                if (!navigator.onLine) {
                    this.$dispatch('toast', 'No internet connection. Please check your network.');
                    return;
                }
                event.target.submit();
            }
        }));
    });

    // Password Constraints Script
    function checkResetConstraints() {
        const pw = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;

        const upper = /[A-Z]/.test(pw);
        const number = /[0-9]/.test(pw);
        const special = /[^A-Za-z0-9]/.test(pw);
        const length = pw.length >= 8;

        setC('rc-upper', upper);
        setC('rc-number', number);
        setC('rc-special', special);
        setC('rc-length', length);

        const match = pw === confirm && confirm.length > 0;
        const matchErr = document.getElementById('matchError');
        if (confirm.length > 0 && !match) {
            matchErr.classList.remove('hidden');
        } else {
            matchErr.classList.add('hidden');
        }

        document.getElementById('resetBtn').disabled = !(upper && number && special && length && match);
    }

    function setC(id, met) {
        const el = document.getElementById(id);
        if (met) {
            el.className = 'text-emerald-500 flex items-center gap-2 font-medium';
            el.querySelector('i').className = 'fa-solid fa-circle-check text-[10px]';
        } else {
            el.className = 'text-slate-400 flex items-center gap-2';
            el.querySelector('i').className = 'fa-solid fa-circle text-[5px]';
        }
    }
</script>
