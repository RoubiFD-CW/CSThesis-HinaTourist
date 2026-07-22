<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Site Pass') }} | Visitor Entry</title>
    @include('partials.head')
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

<body class="antialiased bg-white text-slate-800 min-h-screen flex flex-col justify-center py-2 sm:py-6"
    x-data="visitorPass()">

    <div class="relative py-2 sm:max-w-xl sm:mx-auto w-full px-4">
        <div class="relative px-4 py-6 bg-white shadow-2xl border border-slate-100 sm:rounded-3xl sm:px-10 sm:py-8">

            <div class="max-w-md mx-auto">
                <div class="flex items-center gap-3 mb-4">
                    <div class="shrink-0">
                        <img src="{{ asset('hinatourist-logo.png') }}" class="w-12 h-12 object-contain" alt="Logo">
                    </div>
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ config('app.name') }}</span>
                </div>

                <!-- Form Section -->
                <div x-show="!generatedQR" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                    <div class="divide-y divide-gray-200">
                        <div class="py-2 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                            <h2 class="text-xl font-bold text-slate-800 mb-1"
                                x-text="'Visitor Entry Pass for ' + spotName"></h2>
                            <p class="text-sm text-slate-500 mb-6">Please fill out this form to generate your entry
                                pass.</p>

                            <form @submit.prevent="generatePass">
                                <!-- Visitor Type -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Are you:</label>
                                    <div class="flex items-center gap-4">
                                        <label
                                            class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 hover:border-[#008080]/40 transition-colors">
                                            <input type="radio" name="visitor_type" value="Local"
                                                x-model="form.visitor_type"
                                                class="text-[#008080] focus:ring-[#008080]">
                                            <span class="text-sm text-slate-700">Local Resident</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 hover:border-[#008080]/40 transition-colors">
                                            <input type="radio" name="visitor_type" value="Foreign Tourist"
                                                x-model="form.visitor_type"
                                                class="text-[#008080] focus:ring-[#008080]">
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
                                            :class="errors.male_count ? 'border-rose-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-rose-50/50' : 'border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080]'">
                                        <p x-show="errors.male_count" x-text="errors.male_count" x-cloak
                                            class="text-xs text-rose-500 mt-1"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Female</label>
                                        <input type="text" inputmode="numeric" x-model="form.female_count" required
                                            placeholder="0" @input="validateNumber('female_count')"
                                            class="w-full px-4 py-2.5 rounded-xl border outline-none transition-all"
                                            :class="errors.female_count ? 'border-rose-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-rose-50/50' : 'border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080]'">
                                        <p x-show="errors.female_count" x-text="errors.female_count" x-cloak
                                            class="text-xs text-rose-500 mt-1"></p>
                                    </div>
                                </div>

                                <!-- Group Size (auto-computed) -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Group Size (Male
                                        + Female)</label>
                                    <div class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed"
                                        :class="((parseInt(form.male_count, 10) || 0) + (parseInt(form.female_count, 10) || 0)) > 1000 ? 'border-rose-400 bg-rose-50 text-rose-600' : ''"
                                        x-text="(parseInt(form.male_count, 10) || 0) + (parseInt(form.female_count, 10) || 0) || '0'">
                                    </div>
                                    <p x-show="((parseInt(form.male_count, 10) || 0) + (parseInt(form.female_count, 10) || 0)) > 1000"
                                        class="text-xs text-rose-500 mt-1 font-bold" x-cloak>
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Maximum of 1000 people per
                                        group only.
                                    </p>
                                </div>

                                <!-- Origin -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Where are you
                                        from?</label>
                                    <div class="relative" x-data="{ 
                                            open: false, 
                                            options: { 'Local': ['Within the province', 'Other province'], 'Foreign Tourist': ['Foreign country residence'] }, 
                                            selectOption(val) { form.origin = val; this.open = false; } 
                                        }" @click.away="open = false">
                                        <div @click="open = !open" tabindex="0"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white focus-within:ring-2 focus-within:ring-[#008080]/20 focus-within:border-[#008080] outline-none transition-all cursor-pointer flex justify-between items-center text-base">
                                            <span x-text="form.origin || 'Select origin...'"
                                                :class="form.origin ? 'text-slate-900' : 'text-slate-500 font-normal'"></span>
                                            <i class="fa-solid fa-chevron-down text-slate-400 transition-transform text-sm relative top-[2px]"
                                                :class="open ? 'rotate-180' : ''"></i>
                                        </div>
                                        <div x-show="open" x-transition.opacity.duration.200ms
                                            class="absolute z-50 w-full mt-1 top-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden py-1"
                                            style="display: none;">
                                            <template x-for="opt in options[form.visitor_type] || []" :key="opt">
                                                <div @click="selectOption(opt)"
                                                    class="px-4 py-2.5 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                                    :class="form.origin === opt ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
                                                    x-text="opt"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reason -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Reason for
                                        visit</label>
                                    <div class="relative" x-data="{ 
                                            open: false, 
                                            options: ['Vacation or Leisure', 'Business', 'Others'],
                                            selectOption(val) { form.visit_reason = val; this.open = false; } 
                                        }" @click.away="open = false">
                                        <div @click="open = !open" tabindex="0"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white focus-within:ring-2 focus-within:ring-[#008080]/20 focus-within:border-[#008080] outline-none transition-all cursor-pointer flex justify-between items-center text-base">
                                            <span x-text="form.visit_reason || 'Select reason...'"
                                                :class="form.visit_reason ? 'text-slate-900' : 'text-slate-500 font-normal'"></span>
                                            <i class="fa-solid fa-chevron-down text-slate-400 transition-transform text-sm relative top-[2px]"
                                                :class="open ? 'rotate-180' : ''"></i>
                                        </div>
                                        <div x-show="open" x-transition.opacity.duration.200ms
                                            class="absolute z-50 w-full mt-1 top-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden py-1"
                                            style="display: none;">
                                            <template x-for="opt in options" :key="opt">
                                                <div @click="selectOption(opt)"
                                                    class="px-4 py-2.5 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                                    :class="form.visit_reason === opt ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
                                                    x-text="opt"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other Reason Specify -->
                                <div class="mb-4" x-show="form.visit_reason === 'Others'" x-transition>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Specify Reason</label>
                                    <input type="text" x-model="form.visit_reason_other" placeholder="Please specify"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                                </div>

                                <button type="submit"
                                    class="w-full py-3 bg-[#008080] hover:bg-[#006666] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#008080]/20 active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
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
                        <h2 class="text-2xl font-semibold text-slate-800 mb-2">You're All Set!</h2>
                        <p class="text-slate-500 mb-6">Please show this QR code to the site attendant.</p>

                        <div
                            class="bg-white p-6 rounded-xl border-2 border-dashed border-slate-300 inline-block mb-6 shadow-sm">
                            <canvas id="qrcode-canvas" class="mx-auto"></canvas>
                        </div>

                        <div
                            class="bg-[#008080]/5 p-5 rounded-xl text-left text-sm text-slate-800 border border-[#008080]/10">
                            <p class="font-semibold text-slate-900 mb-3 border-b border-[#008080]/10 pb-2">Visitor Details:</p>
                            <div class="flex flex-col gap-2.5">
                                <div><span class="text-[#006666] font-medium">Type:</span> <span class="text-slate-800" x-text="form.visitor_type"></span></div>
                                <div><span class="text-[#006666] font-medium">Size:</span> <span class="text-slate-800" x-text="form.group_size"></span></div>
                                <div><span class="text-[#006666] font-medium">Origin:</span> <span class="text-slate-800" x-text="form.origin"></span></div>
                                <div><span class="text-[#006666] font-medium">Spot:</span> <span class="text-slate-800" x-text="spotName"></span></div>
                            </div>
                        </div>

                        <button @click="resetForm"
                            class="mt-8 text-[#008080] font-medium hover:text-[#006666] transition-colors text-sm flex items-center justify-center w-full">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Create Another Pass
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
                    visit_reason: 'Vacation or Leisure',
                    visit_reason_other: '',
                    dedicated_area: '',
                    timestamp: ''
                },
                errors: { male_count: '', female_count: '' },
                spotName: 'Hinatuan Tourism',

                init() {
                    const urlParams = new URLSearchParams(window.location.search);

                    if (urlParams.has('name')) {
                        this.spotName = urlParams.get('name');
                    }

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

                    if (this.form.group_size > 1000) {
                        this.errors.male_count = 'Maximum group size is 1000 people.';
                        return;
                    }

                    if (!this.form.origin) {
                        alert("Please select where you are from.");
                        return;
                    }

                    if (this.form.visit_reason === 'Others' && !this.form.visit_reason_other.trim()) {
                        alert("Please specify your reason for visit.");
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
                    this.form.visit_reason = 'Vacation or Leisure';
                    this.form.visit_reason_other = '';
                    this.errors = { male_count: '', female_count: '' };
                }
            }
        }
    </script>
</body>

</html>