<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PWA App') }} | Logbook</title>
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

<body x-data="{ sidebarOpen: false }"
    class="antialiased bg-slate-50 text-slate-800 selection:bg-[#008080]/100 selection:text-white h-screen overflow-y-auto flex flex-col lg:flex-row">

    {{-- Navigation --}}
    @include('dashboard.partials.sidebar')

    {{-- Mobile Header --}}
    <div class="lg:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="flex items-center gap-2">
            <img src="{{ asset('hinatourist-logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
            <span class="font-bold text-slate-800">{{ config('app.name') }}</span>
        </div>
        <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700 p-2">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col relative overflow-y-auto w-full p-4 sm:p-8">
        <!-- Background Decor -->
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-[15%] right-[10%] w-[35%] h-[35%] rounded-full bg-indigo-100/40 blur-[100px]">
            </div>
            <div class="absolute bottom-[10%] left-[5%] w-[30%] h-[30%] rounded-full bg-cyan-100/30 blur-[80px]"></div>
        </div>

        <div class="w-full px-0 sm:px-2 py-2 sm:py-4" x-data="logbook()">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 mb-1">
                        Visitor Logbook
                    </h1>
                    <p class="text-slate-500 text-sm">Record visitor check-ins and details.</p>
                </div>

                {{-- Sync Status Indicator --}}
                <div
                    class="flex items-center gap-3 bg-white/50 backdrop-blur px-4 py-2 rounded-xl border border-white/20">
                    <span x-show="!online" class="text-amber-600 text-sm font-bold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-wifi-slash"></i> Offline Mode
                    </span>
                    <span x-show="online && pendingLogs.length > 0"
                        class="text-[#008080] text-sm font-bold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-sync fa-spin"></i> Syncing...
                    </span>
                    <span x-show="online && pendingLogs.length === 0"
                        class="text-[#008080] text-sm font-bold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-check-circle"></i> Synced
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8">
                {{-- Log Entry Form --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white/80 backdrop-blur-sm p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200 lg:sticky lg:top-4">
                        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-[#008080]"></i> New Entry
                        </h2>
                        <form @submit.prevent="saveLog">
                            <!-- Visitor Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Are you:</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="visitor_type" value="Local"
                                            x-model="form.visitor_type" class="text-[#008080] focus:ring-[#008080]">
                                        <span class="text-sm text-slate-700">Local Resident</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="visitor_type" value="Foreign Tourist"
                                            x-model="form.visitor_type" class="text-[#008080] focus:ring-[#008080]">
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
                                        class="text-xs text-rose-500 mt-1 flex items-center gap-1"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Female</label>
                                    <input type="text" inputmode="numeric" x-model="form.female_count" required
                                        placeholder="0" @input="validateNumber('female_count')"
                                        class="w-full px-4 py-2.5 rounded-xl border outline-none transition-all"
                                        :class="errors.female_count ? 'border-rose-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-rose-50/50' : 'border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080]'">
                                    <p x-show="errors.female_count" x-text="errors.female_count" x-cloak
                                        class="text-xs text-rose-500 mt-1 flex items-center gap-1"></p>
                                </div>
                            </div>

                            <!-- Group Size (auto-computed) -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Group Size (Male +
                                    Female)</label>
                                <div class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed"
                                    x-text="(parseInt(form.male_count, 10) || 0) + (parseInt(form.female_count, 10) || 0) || '0'">
                                </div>
                            </div>

                            <!-- Origin -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Where are you from?</label>
                                <select x-model="form.origin" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                                    <option value="" disabled selected>Select origin...</option>
                                    <template x-if="form.visitor_type === 'Local'">
                                    <optgroup label="Local Options">
                                        <option value="Within the province">Within the province</option>
                                        <option value="Other province">Other province</option>
                                    </optgroup>
                                    </template>
                                    <template x-if="form.visitor_type === 'Foreign Tourist'">
                                        <optgroup label="Foreign Option">
                                            <option value="Foreign country residence">Foreign country residence</option>
                                        </optgroup>
                                    </template>
                                </select>
                            </div>

                            <!-- Reason -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Reason for visit</label>
                                <select x-model="form.visit_reason"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                                    <option value="Vacation/Leisure">Vacation or Leisure</option>
                                    <option value="Business">Business</option>
                                    <option value="Other">Other reason</option>
                                </select>
                            </div>

                            <div class="mb-4" x-show="form.visit_reason === 'Other'" x-transition>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Specify Reason</label>
                                <input type="text" x-model="form.visit_reason_other" placeholder="Please specify"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                            </div>

                            <!-- Dedicated Area (Interactive for Admin, Read-only for Attendants) -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Dedicated Area</label>

                                <div x-data="{
                                    isAdmin: @json(auth()->user()->is_admin),
                                    open: false,
                                    search: form.dedicated_area || '',
                                    options: [
                                        { value: 'Enchanted River', label: 'Enchanted River' },
                                        { value: 'Lodestone Shores Resort', label: 'Lodestone Shores Resort' },
                                        { value: 'Baculin Amazing Sand (Bar)', label: 'Baculin Amazing Sand (Bar)' },
                                        { value: 'Harip Oceanside (White) Beach', label: 'Harip Oceanside (White) Beach' },
                                        { value: 'Rock Island Resort', label: 'Rock Island Resort' },
                                        { value: 'Amparitas Integrated Nature Farm', label: 'Amparitas Integrated Nature Farm' },
                                        { value: 'Sibadan Fish Cage and Resort', label: 'Sibadan Fish Cage and Resort' },
                                        { value: 'Davince Hidden Paradise', label: 'Davince Hidden Paradise' },
                                        { value: 'Hinatuan Adventure Park', label: 'Hinatuan Adventure Park' },
                                        { value: 'Mamaon Beach Resort', label: 'Mamaon Beach Resort' },
                                        { value: 'Landong Bay', label: 'Landong Bay' },
                                        { value: 'Tarusan Cold Spring', label: 'Tarusan Cold Spring' },
                                        { value: 'Llamas Beach Resort', label: 'Llamas Beach Resort' },
                                        { value: 'Puro Brigida\'s Beach', label: 'Puro Brigida\'s Beach' },
                                        { value: 'Bunsadan Falls', label: 'Bunsadan Falls' }
                                    ],
                                    get filteredOptions() {
                                        if (this.search === '') return this.options;
                                        return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    selectOption(option) {
                                        if (!this.isAdmin) return;
                                        form.dedicated_area = option.value;
                                        this.search = option.label;
                                        this.open = false;
                                    },
                                    revertSearch() {
                                        if (!this.isAdmin) return;
                                        const found = this.options.find(o => o.value === form.dedicated_area);
                                        this.search = found ? found.label : '';
                                    }
                                }">
                                    <div class="relative">
                                        <input type="text" x-model="search" :readonly="!isAdmin"
                                            @click="if(isAdmin) open = true" @focus="if(isAdmin) open = true"
                                            @click.away="if(isAdmin) { open = false; revertSearch(); }"
                                            @input="if(isAdmin) open = true"
                                            @keydown.escape="if(isAdmin) { open = false; revertSearch(); }"
                                            @keydown.enter.prevent="if(isAdmin && filteredOptions.length > 0) selectOption(filteredOptions[0])"
                                            :placeholder="isAdmin ? 'Select or search an area' : ''"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none transition-all"
                                            :class="isAdmin ? 'cursor-text bg-white focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080]' : 'bg-slate-100 text-slate-500 cursor-not-allowed'"
                                            autocomplete="off">
                                        <i x-show="isAdmin"
                                            class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform"
                                            :class="open ? 'rotate-180' : ''"></i>

                                        <!-- Dropdown Options -->
                                        <div x-show="isAdmin && open" x-transition.opacity.duration.200ms
                                            class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto"
                                            style="display: none;">
                                            <ul class="py-1 relative top-0 z-[100]">
                                                <template x-for="option in filteredOptions" :key="option.value">
                                                    <li class="px-4 py-2.5 hover:bg-slate-50 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                                        :class="form.dedicated_area === option.value ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
                                                        @click="selectOption(option)">
                                                        <span x-text="option.label"></span>
                                                    </li>
                                                </template>
                                                <div x-show="filteredOptions.length === 0"
                                                    class="px-4 py-3 text-sm text-slate-500 text-center">
                                                    No matches found
                                                </div>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full py-3 bg-[#008080] hover:bg-[#006666] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#008080]-500/20 active:scale-[0.98]">
                                <i class="fa-solid fa-check-circle mr-2"></i> Log Entry
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Log List --}}
                <div class="lg:col-span-3">
                    <div
                        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-5 sm:p-6 border-b border-slate-100">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Recent Logs
                            </h2>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="lg:hidden divide-y divide-slate-100">
                            <template x-for="log in paginatedLogs" :key="log.id || log.local_id">
                                <div class="p-4 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-900 truncate" x-text="log.origin"></p>
                                            <p class="text-xs text-slate-400 mt-0.5"
                                                x-text="log.visitor_type + ' • ' + log.dedicated_area"></p>
                                            <div class="flex flex-col gap-1 mt-2 text-sm text-slate-500">
                                                <span>Group: <span
                                                        x-text="(parseInt(log.male_count) || 0) + (parseInt(log.female_count) || 0)"></span>
                                                    (M:<span x-text="log.male_count"></span>, F:<span
                                                        x-text="log.female_count"></span>)</span>
                                                <span class="text-xs text-slate-400"
                                                    x-text="log.visit_reason === 'Other' ? log.visit_reason_other : log.visit_reason"></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-xs py-0.5 px-2 rounded-full font-medium"
                                                :class="log.pending ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-[#008080]'"
                                                x-text="log.pending ? 'Pending' : 'Synced'">
                                            </span>
                                            <span class="text-xs text-slate-400"
                                                x-text="new Date(log.visit_date || log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="allLogsList.length === 0" class="p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-clipboard-list text-3xl opacity-20"></i>
                                    <p>No logs found.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Desktop Table View --}}
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50/50 text-xs uppercase text-slate-500 border-b border-slate-100">
                                        <th class="px-6 py-4 font-semibold">Origin / Type</th>
                                        <th class="px-6 py-4 font-semibold">Group Stats</th>
                                        <th class="px-6 py-4 font-semibold">Reason</th>
                                        <th class="px-6 py-4 font-semibold text-right">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <template x-for="log in paginatedLogs" :key="log.id || log.local_id">
                                        <tr class="hover:bg-slate-50/50 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:shadow-sm transition-all">
                                                        <i class="fa-solid fa-user text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-slate-900" x-text="log.origin"></p>
                                                        <p class="text-xs text-slate-400" x-text="log.visitor_type"></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col text-sm text-slate-600">
                                                    <span class="font-medium">Total: <span
                                                            x-text="(parseInt(log.male_count) || 0) + (parseInt(log.female_count) || 0)"></span></span>
                                                    <span class="text-xs text-slate-400">
                                                        Male: <span x-text="log.male_count"></span>, Female: <span
                                                            x-text="log.female_count"></span>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-slate-600"
                                                    x-text="log.visit_reason === 'Other' ? log.visit_reason_other : log.visit_reason">
                                                </div>
                                                <div class="text-xs text-slate-400 mt-0.5" x-text="log.dedicated_area">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex flex-col items-end gap-1">
                                                    <span class="text-xs font-bold"
                                                        :class="log.pending ? 'text-amber-500' : 'text-emerald-500'"
                                                        x-text="log.pending ? 'Pending' : 'Synced'"></span>
                                                    <span class="text-xs text-slate-400"
                                                        x-text="new Date(log.visit_date || log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="allLogsList.length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 bg-slate-50/30">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i class="fa-solid fa-clipboard-list text-3xl opacity-20"></i>
                                                <p>No logs found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Controls --}}
                        <div x-show="totalPages > 1" class="p-4 border-t border-slate-100 bg-white/50 flex items-center justify-between gap-4">
                            <div class="text-xs text-slate-500 font-medium">
                                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="currentPage--" :disabled="currentPage <= 1"
                                    class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                                </button>
                                <button @click="currentPage++" :disabled="currentPage >= totalPages"
                                    class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function logbook() {
            return {
                online: navigator.onLine,
                form: {
                    visitor_type: 'Foreign Tourist',
                    group_size: 1,
                    male_count: 0,
                    female_count: 0,
                    origin: '',
                    visit_reason: 'Vacation/Leisure',
                    visit_reason_other: '',
                    dedicated_area: @json(auth()->user()->dedicated_area) || '',
                    visit_date: new Date().toISOString().slice(0, 16)
                },
                logs: [], // Server logs
                pendingLogs: [], // Local logs
                errors: { male_count: '', female_count: '' },
                currentPage: 1,
                perPage: 10,

                get totalPages() {
                    return Math.ceil(this.allLogsList.length / this.perPage) || 1;
                },

                get allLogsList() {
                    return [...this.pendingLogs, ...this.logs];
                },

                get paginatedLogs() {
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    return this.allLogsList.slice(start, end);
                },

                init() {
                    // Check connection status listeners
                    window.addEventListener('online', () => {
                        this.online = true;
                        this.syncLogs();
                    });
                    window.addEventListener('offline', () => this.online = false);

                    // Watch for visitor_type change
                    this.$watch('form.visitor_type', () => {
                        this.form.origin = '';
                    });

                    // Load from LocalStorage
                    this.pendingLogs = JSON.parse(localStorage.getItem('pending_logs') || '[]');

                    // Auto-sync if online
                    if (this.online && this.pendingLogs.length > 0) {
                        this.syncLogs();
                    }

                    // Fetch from server
                    this.fetchLogs();
                },

                fetchLogs() {
                    if (!this.online) return;

                    axios.get('{{ route("api.logs.index") }}')
                        .then(response => {
                            this.logs = response.data;
                        })
                        .catch(err => console.error(err));
                },

                validateNumber(field) {
                    const val = String(this.form[field]);
                    if (val === '') {
                        this.errors[field] = '';
                        return;
                    }
                    if (/[^0-9]/.test(val)) {
                        this.errors[field] = 'Only whole numbers are allowed.';
                        return;
                    }
                    if (val.length > 1 && val.startsWith('0')) {
                        this.errors[field] = 'Invalid format — use "' + parseInt(val, 10) + '" instead of "' + val + '".';
                        return;
                    }
                    if (parseInt(val, 10) < 0) {
                        this.errors[field] = 'Value cannot be negative.';
                        return;
                    }
                    this.errors[field] = '';
                },

                saveLog() {
                    // Run validation on number fields before saving
                    ['male_count', 'female_count'].forEach(f => this.validateNumber(f));
                    if (this.errors.male_count || this.errors.female_count) {
                        return; // Block submission
                    }

                    // Sanitize: convert to proper integers
                    this.form.male_count = parseInt(this.form.male_count, 10) || 0;
                    this.form.female_count = parseInt(this.form.female_count, 10) || 0;
                    // Auto-compute group_size from male + female
                    this.form.group_size = this.form.male_count + this.form.female_count;

                    if (this.form.group_size < 1) {
                        this.errors.male_count = 'Total group must be at least 1 person.';
                        return;
                    }

                    const logEntry = {
                        ...this.form,
                        local_id: Date.now(),
                        visit_date: new Date().toISOString(),
                        created_at: new Date().toISOString(),
                        pending: true
                    };

                    // Add to local queue immediately (optimistic UI)
                    this.pendingLogs.unshift(logEntry);
                    this.saveToStorage();

                    // Reset form but keep area/date/type for convenience
                    this.form.origin = '';
                    this.form.male_count = 0;
                    this.form.female_count = 0;
                    this.form.visit_reason = 'Vacation/Leisure';
                    this.form.visit_reason_other = '';

                    // Try to sync if online
                    if (this.online) {
                        this.syncLogs();
                    }
                },

                syncLogs() {
                    if (this.pendingLogs.length === 0) return;

                    // Filter out logs that are already syncing to avoid race conditions
                    const queue = this.pendingLogs.filter(log => !log.syncing);

                    queue.forEach(log => {
                        log.syncing = true;
                        axios.post('{{ route("api.logs.store") }}', log)
                            .then(response => {
                                // Remove from pending logs
                                this.pendingLogs = this.pendingLogs.filter(l => l.local_id !== log.local_id);
                                this.saveToStorage();

                                // Avoid adding duplicates
                                if (!this.logs.some(l => l.id === response.data.log.id)) {
                                    this.logs.unshift(response.data.log);
                                }
                            })
                            .catch(error => {
                                console.error('Sync failed for log', log, error);
                                if (error.response && error.response.status === 422) {
                                    this.pendingLogs = this.pendingLogs.filter(l => l.local_id !== log.local_id);
                                    this.saveToStorage();
                                    alert('A pending log had invalid data and was discarded.');
                                } else {
                                    log.syncing = false;
                                }
                            });
                    });
                },

                saveToStorage() {
                    localStorage.setItem('pending_logs', JSON.stringify(this.pendingLogs));
                }
            }
        }
    </script>
</body>

</html>

