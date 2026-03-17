<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Site Pass') }} — Visitor Entry</title>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="antialiased bg-white text-slate-800 min-h-screen flex flex-col justify-center py-6 sm:py-12"
    x-data="visitorPass()">

    <div class="relative py-3 sm:max-w-xl sm:mx-auto w-full px-4">
        <div class="relative px-4 py-10 bg-white shadow-2xl border border-slate-100 sm:rounded-3xl sm:p-10">

            <div class="max-w-md mx-auto">
                <div class="flex items-center gap-3 mb-6">
                    <div class="shrink-0">
                        <img src="{{ asset('hinatourist-logo.png') }}" class="w-12 h-12 object-contain" alt="Logo">
                    </div>
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ config('app.name') }}</span>
                </div>

                <!-- Form Section -->
                <div x-show="!generatedQR" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                    <div class="divide-y divide-gray-200">
                        <div class="py-4 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                            <h2 class="text-xl font-bold text-slate-800 mb-1">Visitor Entry Pass</h2>
                            <p class="text-sm text-slate-500 mb-6">Please fill out this form to generate your entry
                                pass.</p>

                            <form @submit.prevent="generatePass">
                                <!-- Visitor Type -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Are you:</label>
                                    <div class="flex items-center gap-4">
                                        <label
                                            class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 hover:border-indigo-300 transition-colors">
                                            <input type="radio" name="visitor_type" value="Local"
                                                x-model="form.visitor_type"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-slate-700">Local Resident</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 hover:border-indigo-300 transition-colors">
                                            <input type="radio" name="visitor_type" value="Foreign Tourist"
                                                x-model="form.visitor_type"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-slate-700">Foreign Tourist</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Gender Count -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Male</label>
                                        <input type="text" inputmode="numeric" x-model="form.male_count" required
                                            placeholder="0" @input="validateNumber('male_count')"
                                            class="w-full px-4 py-2.5 rounded-xl border outline-none transition-all"
                                            :class="errors.male_count ? 'border-rose-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-rose-50/50' : 'border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500'">
                                        <p x-show="errors.male_count" x-text="errors.male_count" x-cloak
                                            class="text-xs text-rose-500 mt-1"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Female</label>
                                        <input type="text" inputmode="numeric" x-model="form.female_count" required
                                            placeholder="0" @input="validateNumber('female_count')"
                                            class="w-full px-4 py-2.5 rounded-xl border outline-none transition-all"
                                            :class="errors.female_count ? 'border-rose-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-rose-50/50' : 'border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500'">
                                        <p x-show="errors.female_count" x-text="errors.female_count" x-cloak
                                            class="text-xs text-rose-500 mt-1"></p>
                                    </div>
                                </div>

                                <!-- Group Size (auto-computed) -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Group Size (Male
                                        + Female)</label>
                                    <div class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed"
                                        x-text="(parseInt(form.male_count, 10) || 0) + (parseInt(form.female_count, 10) || 0) || '0'">
                                    </div>
                                </div>

                                <!-- Origin -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Where are you
                                        from?</label>
                                    <select x-model="form.origin" required
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                                        <option value="" disabled selected>Select origin...</option>
                                        <template x-if="form.visitor_type === 'Local'">
                                            <optgroup label="Local Options">
                                                <option value="Within the province">Within the province</option>
                                                <option value="Other province">Other province</option>
                                            </optgroup>
                                        </template>
                                        <template x-if="form.visitor_type === 'Foreign Tourist'">
                                            <optgroup label="Foreign Option">
                                                <option value="Foreign country residence">Foreign country residence
                                                </option>
                                            </optgroup>
                                        </template>
                                    </select>
                                </div>

                                <!-- Reason -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Reason for
                                        visit</label>
                                    <select x-model="form.visit_reason"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                                        <option value="Vacation/Leisure">Vacation or Leisure</option>
                                        <option value="Business">Business</option>
                                        <option value="Other">Other reason</option>
                                    </select>
                                </div>

                                <!-- Other Reason Specify -->
                                <div class="mb-4" x-show="form.visit_reason === 'Other'" x-transition>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Specify Reason</label>
                                    <input type="text" x-model="form.visit_reason_other" placeholder="Please specify"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                                </div>

                                <button type="submit"
                                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-qrcode"></i> Generate Entry Pass
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- QR Display Section -->
                <div x-show="generatedQR" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-center py-6">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-4">
                            <i class="fa-solid fa-check text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">You're All Set!</h2>
                        <p class="text-slate-500 mb-6">Please show this QR code to the site attendant.</p>

                        <div
                            class="bg-white p-4 rounded-xl border-2 border-dashed border-slate-300 inline-block mb-6 shadow-sm">
                            <canvas id="qrcode-canvas" class="mx-auto"></canvas>
                        </div>

                        <div
                            class="bg-slate-50 p-4 rounded-xl text-left text-sm text-slate-600 border border-slate-100">
                            <p class="font-bold text-slate-800 mb-2 border-b border-slate-200 pb-2">Visitor Details:</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div><span class="text-slate-400">Type:</span> <span x-text="form.visitor_type"></span>
                                </div>
                                <div><span class="text-slate-400">Size:</span> <span x-text="form.group_size"></span>
                                </div>
                                <div class="col-span-2"><span class="text-slate-400">Origin:</span> <span
                                        x-text="form.origin"></span></div>
                            </div>
                        </div>

                        <button @click="resetForm"
                            class="mt-8 text-indigo-600 font-semibold hover:text-indigo-800 transition-colors text-sm">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Create Another Pass
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function visitorPass() {
            return {
                generatedQR: false,
                form: {
                    visitor_type: 'Foreign Tourist',
                    group_size: 1,
                    male_count: '',
                    female_count: '',
                    origin: '',
                    visit_reason: 'Vacation/Leisure',
                    visit_reason_other: '',
                    dedicated_area: '',
                    timestamp: ''
                },
                errors: { male_count: '', female_count: '' },

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('area')) {
                        this.form.dedicated_area = urlParams.get('area');
                    }

                    // Watch for changes in visitor_type to reset origin if it doesn't match the new options
                    this.$watch('form.visitor_type', (value) => {
                        this.form.origin = '';
                    });
                },

                validateNumber(field) {
                    const val = String(this.form[field]);
                    if (val === '') { this.errors[field] = ''; return; }
                    if (/[^0-9]/.test(val)) { this.errors[field] = 'Only whole numbers are allowed.'; return; }
                    if (val.length > 1 && val.startsWith('0')) { this.errors[field] = 'Invalid format — use "' + parseInt(val, 10) + '" instead of "' + val + '".'; return; }
                    if (parseInt(val, 10) < 0) { this.errors[field] = 'Value cannot be negative.'; return; }
                    this.errors[field] = '';
                },

                generatePass() {
                    // Validate
                    ['male_count', 'female_count'].forEach(f => this.validateNumber(f));
                    if (this.errors.male_count || this.errors.female_count) return;

                    // Sanitize
                    this.form.male_count = parseInt(this.form.male_count, 10) || 0;
                    this.form.female_count = parseInt(this.form.female_count, 10) || 0;
                    this.form.group_size = this.form.male_count + this.form.female_count;

                    if (this.form.group_size < 1) {
                        this.errors.male_count = 'Total group must be at least 1 person.';
                        return;
                    }

                    this.form.timestamp = new Date().toISOString();
                    const payload = JSON.stringify(this.form);
                    const canvas = document.getElementById('qrcode-canvas');

                    if (window.QRCode) {
                        window.QRCode.toCanvas(canvas, payload, {
                            width: 200,
                            margin: 2,
                            color: { dark: '#0F172A', light: '#FFFFFF' }
                        }, (error) => {
                            if (error) console.error(error);
                            else {
                                this.generatedQR = true;
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        });
                    } else {
                        alert('QR Code library not loaded properly. Please refresh.');
                    }
                },

                resetForm() {
                    this.generatedQR = false;
                    this.form.group_size = 1;
                    this.form.male_count = '';
                    this.form.female_count = '';
                    this.form.origin = '';
                    this.form.visit_reason = 'Vacation/Leisure';
                    this.form.visit_reason_other = '';
                    this.errors = { male_count: '', female_count: '' };
                }
            }
        }
    </script>
</body>

</html>