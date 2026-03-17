<x-guest-layout>
    <!-- Step 1: Email (visible by default) -->
    <div id="step-email">
        <h2 class="text-2xl font-bold text-slate-900 text-center mb-1">Forgot Password</h2>
        <p class="text-sm text-slate-400 text-center mb-8">Enter your email to receive a verification code
        </p>

        <!-- Messages -->
        <div id="errorBox" class="hidden mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600">
        </div>
        <div id="successBox"
            class="hidden mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-600">
        </div>

        <div class="mb-6">
            <label for="reset-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email
                Address</label>
            <input type="email" id="reset-email" required
                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white/50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-400 transition-all duration-200"
                placeholder="you@example.com">
        </div>

        <button type="button" onclick="sendResetOtp()" id="sendResetOtpBtn"
            class="w-full py-3.5 px-6 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.97]">
            Send Verification Code
        </button>
    </div>

    <!-- Step 2: OTP + New Password (hidden by default) -->
    <div id="step-reset" class="hidden">
        <h2 class="text-2xl font-bold text-slate-900 text-center mb-1">Reset Password</h2>
        <p class="text-sm text-slate-400 text-center mb-8">Enter the code sent to <span id="resetEmailDisplay"
                class="font-semibold text-slate-600"></span></p>

        <!-- Messages -->
        <div id="errorBox2" class="hidden mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-600">
        </div>
        <div id="successBox2"
            class="hidden mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-600">
        </div>

        <!-- OTP -->
        <div class="mb-5">
            <label for="reset-otp" class="block text-sm font-medium text-slate-700 mb-1.5">Verification
                Code</label>
            <input type="text" id="reset-otp" maxlength="6" required
                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white/50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-400 transition-all duration-200 text-center text-2xl tracking-[0.5em] font-bold"
                placeholder="000000">
        </div>

        <div x-data="{ showNew: false, showConfirm: false }">
            <!-- New Password -->
            <div class="mb-4 relative">
                <label for="new-password" class="block text-sm font-medium text-slate-700 mb-1.5">New
                    Password</label>
                <input :type="showNew ? 'text' : 'password'" id="new-password" required
                    oninput="checkResetConstraints()"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white/50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-400 transition-all duration-200 pr-12"
                    placeholder="••••••••">
                <button type="button" @click="showNew = !showNew"
                    class="absolute right-3 top-[38px] text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-regular" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>

            <!-- Password Constraints -->
            <div class="mb-5 p-3 bg-slate-50/80 rounded-xl text-xs space-y-1.5 border border-slate-100">
                <p id="rc-upper" class="text-slate-400 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> At least one uppercase letter
                </p>
                <p id="rc-number" class="text-slate-400 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> At least one number
                </p>
                <p id="rc-special" class="text-slate-400 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> At least one special character
                </p>
                <p id="rc-length" class="text-slate-400 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> Minimum 8 characters
                </p>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6 relative">
                <label for="new-password-confirm" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm
                    Password</label>
                <input :type="showConfirm ? 'text' : 'password'" id="new-password-confirm" required
                    oninput="checkResetConstraints()"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white/50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-400 transition-all duration-200 pr-12"
                    placeholder="••••••••">
                <button type="button" @click="showConfirm = !showConfirm"
                    class="absolute right-3 top-[38px] text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-regular" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
                <p id="matchError" class="hidden mt-1.5 text-xs text-rose-500">Passwords do not match</p>
            </div>
        </div>

        <button type="button" onclick="resetPassword()" id="resetBtn" disabled
            class="w-full py-3.5 px-6 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.97] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-cyan-500/20">
            Reset Password
        </button>

        <button type="button" onclick="goBackToEmail()"
            class="w-full mt-3 py-3 text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors">
            ← Change email
        </button>
    </div>

    <!-- Back to Login -->
    <p class="mt-6 text-center text-sm text-slate-500">
        Remember your password?
        <a href="/login" class="text-cyan-600 hover:text-cyan-700 font-semibold transition-colors">
            Sign in
        </a>
    </p>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ========================
        // Messages
        // ========================
        function showError(boxId, msg) {
            const box = document.getElementById(boxId);
            box.textContent = msg;
            box.classList.remove('hidden');
            // Hide the other box in same step
            const successId = boxId === 'errorBox' ? 'successBox' : 'successBox2';
            document.getElementById(successId).classList.add('hidden');
        }

        function showSuccess(boxId, msg) {
            const box = document.getElementById(boxId);
            box.textContent = msg;
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
            btn.textContent = 'Sending...';

            try {
                const res = await fetch('/forgot-password/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        email
                    })
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    document.getElementById('resetEmailDisplay').textContent = email;
                    document.getElementById('step-email').classList.add('hidden');
                    document.getElementById('step-reset').classList.remove('hidden');
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
            btn.textContent = 'Send Verification Code';
        }

        // ========================
        // Step 2: Reset Password
        // ========================
        async function resetPassword() {
            const email = document.getElementById('reset-email').value.trim();
            const otp = document.getElementById('reset-otp').value.trim();
            const password = document.getElementById('new-password').value;
            const passwordConfirm = document.getElementById('new-password-confirm').value;

            if (!otp) {
                showError('errorBox2', 'Please enter the verification code.');
                return;
            }

            const btn = document.getElementById('resetBtn');
            btn.disabled = true;
            btn.textContent = 'Resetting...';

            try {
                const res = await fetch('/forgot-password/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
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
                    showSuccess('successBox2', data.message || 'Password reset successfully!');
                    // Redirect to login after a short delay
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        showError('errorBox2', Array.isArray(firstError) ? firstError[0] : firstError);
                    } else {
                        showError('errorBox2', data.message || 'Failed to reset password.');
                    }
                    btn.disabled = false;
                    btn.textContent = 'Reset Password';
                }
            } catch (e) {
                showError('errorBox2', 'Network error. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Reset Password';
            }
        }

        // ========================
        // Go back to email step
        // ========================
        function goBackToEmail() {
            document.getElementById('step-reset').classList.add('hidden');
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
            document.getElementById(id).className = met ?
                'text-green-600 flex items-center gap-2 font-medium' :
                'text-slate-400 flex items-center gap-2';
        }
    </script>
</x-guest-layout>