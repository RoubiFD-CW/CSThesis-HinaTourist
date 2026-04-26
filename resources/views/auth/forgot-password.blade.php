<x-guest-layout>
<div x-data="{ showSuccessModal: false }" @password-reset-success.window="showSuccessModal = true">
    <!-- Back to Home -->
    <a href="/home" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-slate-600 transition-colors group mb-5">
        <i class="fa-solid fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-0.5"></i>
        Back to Home
    </a>

    <!-- Step 1: Email (visible by default) -->
    <div id="step-email">
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Forgot Password</h2>
            <p class="text-sm text-slate-400 mt-1">Enter your email to receive a verification code</p>
        </div>

        <!-- Messages -->
        <div id="errorBox" class="hidden mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600 flex items-start gap-2.5">
        </div>
        <div id="successBox" class="hidden mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-600 flex items-center gap-2.5">
        </div>

        <div class="mb-5">
            <label for="reset-email" class="block text-sm font-medium text-slate-600 mb-1.5">Email Address</label>
            <div class="relative">
                <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="email" id="reset-email" required
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                    placeholder="you@gmail.com">
            </div>
        </div>

        <button type="button" onclick="sendResetOtp()" id="sendResetOtpBtn"
            class="w-full py-2.5 px-5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98]">
            <i class="fa-solid fa-paper-plane mr-1.5"></i>
            Send Verification Code
        </button>
    </div>

    <!-- Step 2: OTP Verification -->
    <div id="step-otp" class="hidden">
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-shield-halved text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Verify Code</h2>
            <p class="text-sm text-slate-400 mt-1">Enter the code sent to <span id="resetEmailDisplay" class="font-semibold text-slate-600"></span></p>
        </div>

        <!-- Messages -->
        <div id="errorBox2" class="hidden mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600">
        </div>
        <div id="successBox2" class="hidden mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-600">
        </div>

        <!-- OTP -->
        <div class="mb-5">
            <label for="reset-otp" class="block text-sm font-medium text-slate-600 mb-1.5">Verification Code</label>
            <div class="relative">
                <i class="fa-solid fa-hashtag absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="reset-otp" maxlength="6" required
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200 text-center text-xl tracking-[0.4em] font-bold"
                    placeholder="000000">
            </div>
        </div>

        <button type="button" onclick="verifyOtp()" id="verifyOtpBtn"
            class="w-full py-2.5 px-5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98]">
            <i class="fa-solid fa-check mr-1.5"></i>
            Verify Code
        </button>

        <button type="button" onclick="goBackToEmail()"
            class="w-full mt-2.5 py-2.5 text-sm text-slate-400 hover:text-slate-600 font-medium transition-colors flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Change email
        </button>
    </div>

    <!-- Step 3: New Password -->
    <div id="step-password" class="hidden">
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-lock-open text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Set New Password</h2>
            <p class="text-sm text-slate-400 mt-1">Choose a strong password for your account</p>
        </div>

        <!-- Messages -->
        <div id="errorBox3" class="hidden mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600">
        </div>

        <div x-data="{ showNew: false, showConfirm: false }">
            <!-- New Password -->
            <div class="mb-4">
                <label for="new-password" class="block text-sm font-medium text-slate-600 mb-1.5">New Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input :type="showNew ? 'text' : 'password'" id="new-password" required
                        oninput="checkResetConstraints()"
                        class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                        placeholder="Enter new password">
                    <button type="button" @click="showNew = !showNew"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                        <i class="fa-regular text-sm" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
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

            <!-- Confirm Password -->
            <div class="mb-5">
                <label for="new-password-confirm" class="block text-sm font-medium text-slate-600 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input :type="showConfirm ? 'text' : 'password'" id="new-password-confirm" required
                        oninput="checkResetConstraints()"
                        class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 transition-all duration-200"
                        placeholder="Re-enter your password">
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                        <i class="fa-regular text-sm" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <p id="matchError" class="hidden mt-1.5 text-xs text-rose-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Passwords do not match
                </p>
            </div>
        </div>

        <button type="button" onclick="resetPassword()" id="resetBtn" disabled
            class="w-full py-2.5 px-5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:shadow-none">
            <i class="fa-solid fa-check mr-1.5"></i>
            Reset Password
        </button>
    </div>

    <!-- Back to Login -->
    <p class="mt-5 text-center text-sm text-slate-400">
        Remember your password?
        <a href="/login" class="text-[#008080] hover:text-[#006666] font-semibold transition-colors">
            Sign in
        </a>
    </p>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div x-show="showSuccessModal" x-transition.opacity
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="window.location.href='/login'">
        </div>
        <div x-show="showSuccessModal" x-transition.opacity.scale.95
            class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 w-[300px] max-w-[90vw] text-center p-6 sm:p-7">

            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>

            <h3 class="text-lg font-bold text-slate-800 mb-1.5">Reset Successful</h3>
            <p class="text-slate-400 text-sm mb-5 leading-relaxed">
                Your password has been updated. You can now sign in with your new password.
            </p>

            <button type="button" @click="window.location.href='/login'"
                class="w-full py-2.5 px-5 rounded-xl bg-[#008080] text-white text-sm font-semibold shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98]">
                <i class="fa-solid fa-right-to-bracket mr-1.5"></i>
                Continue to Login
            </button>
        </div>
    </div>
</div> <!-- End of x-data -->

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ========================
        // Messages
        // ========================
        function showError(boxId, msg) {
            const box = document.getElementById(boxId);
            box.innerHTML = '<i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-400 shrink-0"></i><span>' + msg + '</span>';
            box.classList.remove('hidden');
            // Hide the other box in same step
            const successId = boxId === 'errorBox' ? 'successBox' : 'successBox2';
            document.getElementById(successId).classList.add('hidden');
        }

        function showSuccess(boxId, msg) {
            const box = document.getElementById(boxId);
            box.innerHTML = '<i class="fa-solid fa-circle-check mt-0.5 text-emerald-400 shrink-0"></i><span>' + msg + '</span>';
            box.classList.remove('hidden');
            const errorId = boxId === 'successBox' ? 'errorBox' : 'errorBox2';
            document.getElementById(errorId).classList.add('hidden');
        }

        // ========================
        // Step 1: Send OTP
        // ========================
        async function sendResetOtp() {
            const email = document.getElementById('reset-email').value.trim();
            if (!email) {
                showError('errorBox', 'Please enter your email.');
                return;
            }

            const btn = document.getElementById('sendResetOtpBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Sending...';

            try {
                const res = await fetch('/forgot-password/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    document.getElementById('resetEmailDisplay').textContent = email;
                    document.getElementById('step-email').classList.add('hidden');
                    document.getElementById('step-otp').classList.remove('hidden');
                    showSuccess('successBox2', data.message || 'Verification code sent!');
                } else {
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        showError('errorBox', Array.isArray(firstError) ? firstError[0] : firstError);
                    } else {
                        showError('errorBox', data.message || 'Failed to send code.');
                    }
                }
            } catch (e) {
                showError('errorBox', 'Network error. Please try again.');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-1.5"></i> Send Verification Code';
        }

        // ========================
        // Step 2: Verify OTP
        // ========================
        async function verifyOtp() {
            const email = document.getElementById('reset-email').value.trim();
            const otp = document.getElementById('reset-otp').value.trim();

            if (!otp) {
                showError('errorBox2', 'Please enter the verification code.');
                return;
            }

            const btn = document.getElementById('verifyOtpBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Verifying...';

            try {
                const res = await fetch('/forgot-password/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email, otp })
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    document.getElementById('step-otp').classList.add('hidden');
                    document.getElementById('step-password').classList.remove('hidden');
                } else {
                    showError('errorBox2', data.message || 'Invalid code.');
                }
            } catch (e) {
                console.error('Verification Error:', e);
                showError('errorBox2', 'Connection error. Please check your internet or try again later.');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i> Verify Code';
        }

        // ========================
        // Step 3: Reset Password
        // ========================
        async function resetPassword() {
            const email = document.getElementById('reset-email').value.trim();
            const otp = document.getElementById('reset-otp').value.trim();
            const password = document.getElementById('new-password').value;
            const passwordConfirm = document.getElementById('new-password-confirm').value;

            const btn = document.getElementById('resetBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Resetting...';

            try {
                const res = await fetch('/forgot-password/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        email,
                        otp,
                        password,
                        password_confirmation: passwordConfirm
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    // Use a more robust way to trigger the modal
                    window.dispatchEvent(new CustomEvent('password-reset-success'));
                } else {
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        showError('errorBox3', Array.isArray(firstError) ? firstError[0] : firstError);
                    } else {
                        showError('errorBox3', data.message || 'Failed to reset password.');
                    }
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i> Reset Password';
                }
            } catch (e) {
                showError('errorBox3', 'Network error. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i> Reset Password';
            }
        }

        // ========================
        // Go back to email step
        // ========================
        function goBackToEmail() {
            document.getElementById('step-otp').classList.add('hidden');
            document.getElementById('step-email').classList.remove('hidden');
        }

        // ========================
        // Password Constraints
        // ========================
        function checkResetConstraints() {
            const pw = document.getElementById('new-password').value;
            const confirm = document.getElementById('new-password-confirm').value;

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
</x-guest-layout>