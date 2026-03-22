<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PWA App') }} | Dashboard</title>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@700&display=swap"
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
    <main class="flex-1 flex flex-col relative overflow-y-auto min-w-0 p-4 sm:p-8 bg-slate-50">
        {{-- Dashboard SVG Background --}}
        <div class="absolute inset-0 top-0 left-0 w-full h-full z-10 pointer-events-none">
            <img src="{{ asset('dashboardimg.svg') }}" alt="Dashboard Background" class="w-full h-full object-cover object-top opacity-100">
        </div>

        <div class="w-full px-0 sm:px-2 py-2 sm:py-4 z-20" x-data="dashboard()">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mb-1">Dashboard</h1>
                    <p class="text-slate-500 text-sm">Welcome back, {{ auth()->user()->name }}!</p>
                </div>
                <div
                    class="flex items-center gap-3 bg-white/50 backdrop-blur px-4 py-2 rounded-xl border border-white/20 shadow-sm h-10">
                    <span x-show="!online" class="text-amber-600 text-sm font-bold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-wifi-slash"></i> Offline
                    </span>
                    <span x-show="online && pendingLogs.length > 0"
                        class="text-[#008080] text-sm font-bold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-sync fa-spin"></i> Syncing
                    </span>
                    <span x-show="online && pendingLogs.length === 0"
                        class="text-[#008080] text-sm font-bold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-check-circle"></i> Synced
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <button @click="showScanner = true"
                    class="bg-white hover:bg-slate-50 relative group overflow-hidden p-6 rounded-2xl border border-slate-200 shadow-sm transition-all text-left flex items-start justify-between gap-4">
                    <div class="relative z-10">
                        <div
                            class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xl mb-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Scan Visitor Pass</h3>
                        <p class="text-slate-500 text-sm mt-1">Scan a tourist's digital QR pass to verify and log
                             entry.</p>
                    </div>
                </button>
                <button @click="openGenerator()"
                    class="bg-white hover:bg-slate-50 relative group overflow-hidden p-6 rounded-2xl border border-slate-200 shadow-sm transition-all text-left flex items-start justify-between gap-4">
                    <div class="relative z-10">
                        <div
                            class="w-12 h-12 rounded-xl bg-[#008080] text-white flex items-center justify-center text-xl mb-3 shadow-lg shadow-[#008080]/20 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-print"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Generate Site QR</h3>
                        <p class="text-slate-500 text-sm mt-1">Create and print a QR code poster for your area.</p>
                    </div>
                </button>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Recent Activity
                        <span class="ml-auto bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold" style="font-family: 'Poppins', sans-serif;">{{ $totalLogs }} Logs Total</span>
                    </h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <template x-for="log in allLogs.slice(0, 5)" :key="log.id || log.local_id">
                        <div class="p-4 hover:bg-slate-50/50 transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate w-32 sm:w-auto" x-text="log.origin">
                                    </p>
                                    <p class="text-xs text-slate-500"
                                        x-text="log.visitor_type + ' - ' + log.group_size + ' pax'"></p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="block text-xs font-medium"
                                    :class="log.pending ? 'text-amber-600' : 'text-[#008080]'"
                                    x-text="log.pending ? 'Pending' : 'Synced'"></span>
                                <span class="text-xs text-slate-400 mt-0.5"
                                    x-text="new Date(log.visit_date || log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="allLogs.length === 0" class="p-8 text-center text-slate-400">
                        No recent scans or logs found.
                    </div>
                    <div x-show="allLogs.length > 0" class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                        <a href="{{ route('logbook.index') }}"
                            class="text-sm text-[#008080] font-medium hover:text-[#008080]">View Full Logbook
                            &rarr;</a>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- Tourist Destinations --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div class="mt-8">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-[#008080]"></i> Tourist Destinations
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5">All registered tourist spots in Hinatuan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

                    {{-- 1. Enchanted River --}}
                    <div
                        class="relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/enchanted.jpg') }}" alt="Enchanted River" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Enchanted River</h3>
                            <p class="text-[11px] text-slate-400">Natural Wonder</p>
                        </div>
                    </div>

                    {{-- 2. Lodestone Shores Resort --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/lodestone.jpg') }}" alt="Lodestone Shores Resort" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Lodestone Shores Resort</h3>
                            <p class="text-[11px] text-slate-400">Beach Resort</p>
                        </div>
                    </div>

                    {{-- 3. Baculin Amazing Sand (Bar) --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/baculinsandbar.jpg') }}" alt="Baculin Amazing Sand Bar" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Baculin Amazing Sand (Bar)</h3>
                            <p class="text-[11px] text-slate-400">Sandbar</p>
                        </div>
                    </div>

                    {{-- 4. Harip Oceanside (White) Beach --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/harip.jpg') }}" alt="Harip Oceanside Beach" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Harip Oceanside (White) Beach</h3>
                            <p class="text-[11px] text-slate-400">White Beach</p>
                        </div>
                    </div>

                    {{-- 5. Rock Island Resort --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/rockisland.jpg') }}" alt="Rock Island Resort" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Rock Island Resort</h3>
                            <p class="text-[11px] text-slate-400">Island Resort</p>
                        </div>
                    </div>

                    {{-- 6. Amparitas Integrated Nature Farm --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/amparitas.jpg') }}" alt="Amparitas Integrated Nature Farm" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Amparitas Integrated Nature Farm</h3>
                            <p class="text-[11px] text-slate-400">Nature Farm</p>
                        </div>
                    </div>

                    {{-- 7. Sibadan Fish Cage and Resort --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/sibadan.jpg') }}" alt="Sibadan Fish Cage and Resort" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Sibadan Fish Cage and Resort</h3>
                            <p class="text-[11px] text-slate-400">Fish Cage & Resort</p>
                        </div>
                    </div>

                    {{-- 8. Davince Hidden Paradise --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/davince.jpg') }}" alt="Davince Hidden Paradise" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Davince Hidden Paradise</h3>
                            <p class="text-[11px] text-slate-400">Hidden Paradise</p>
                        </div>
                    </div>

                    {{-- 9. Hinatuan Adventure Park --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/adventurepark.jpg') }}" alt="Hinatuan Adventure Park" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Hinatuan Adventure Park</h3>
                            <p class="text-[11px] text-slate-400">Adventure Park</p>
                        </div>
                    </div>

                    {{-- 10. Mamaon Beach Resort --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/mamaon.jpg') }}" alt="Mamaon Beach Resort" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Mamaon Beach Resort</h3>
                            <p class="text-[11px] text-slate-400">Beach Resort</p>
                        </div>
                    </div>

                    {{-- 11. Landong Bay --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/landongbay.jpg') }}" alt="Landong Bay" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Landong Bay</h3>
                            <p class="text-[11px] text-slate-400">Bay & Dock</p>
                        </div>
                    </div>

                    {{-- 12. Tarusan Cold Spring --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/tarusan.jpg') }}" alt="Tarusan Cold Spring" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Tarusan Cold Spring</h3>
                            <p class="text-[11px] text-slate-400">Cold Spring</p>
                        </div>
                    </div>

                    {{-- 13. Llamas Beach Resort --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/llamas.jpg') }}" alt="Llamas Beach Resort" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Llamas Beach Resort</h3>
                            <p class="text-[11px] text-slate-400">Beach Resort</p>
                        </div>
                    </div>

                    {{-- 14. Puro Brigida's Beach --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/purobrigada.jpg') }}" alt="Puro Brigida's Beach" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Puro Brigida's Beach</h3>
                            <p class="text-[11px] text-slate-400">Island Beach</p>
                        </div>
                    </div>

                    {{-- 15. Bunsadan Falls --}}
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-sm border border-slate-200 shadow-sm">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('HinaTouristImages/bunsadan.png') }}" alt="Bunsadan Falls" loading="lazy" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-white p-3">
                            <h3 class="font-bold text-sm text-slate-800 truncate">Bunsadan Falls</h3>
                            <p class="text-[11px] text-slate-400">Waterfall</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Scanner Modal --}}
            <div x-show="showScanner" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative" @click.away="closeScanner()">
                    <button @click="closeScanner()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Scan Visitor Pass</h3>
                    <div id="reader" class="w-full rounded-xl overflow-hidden bg-slate-100"></div>
                    <p class="text-xs text-center text-slate-400 mt-4">Point the camera at the visitor's QR code</p>
                </div>
            </div>

            {{-- Generator Modal --}}
            <div x-show="showGenerator" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
                x-transition>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative">
                    <button @click="showGenerator = false"
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Generate Site QR</h3>
                    <p class="text-sm text-slate-500 mb-6">Print this QR code for visitors to scan and log themselves.
                    </p>
                    <div class="space-y-4">
                        @if(auth()->user()->is_admin)
                            <div class="mb-4" x-data="{
                                        open: false,
                                        search: '',
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
                                            generatorArea = option.value;
                                            this.search = option.label;
                                            this.open = false;
                                            generateSiteQR();
                                        },
                                        initSearch() {
                                            const found = this.options.find(o => o.value === generatorArea);
                                            if (found) this.search = found.label;
                                        },
                                        revertSearch() {
                                            const found = this.options.find(o => o.value === generatorArea);
                                            this.search = found ? found.label : '';
                                        }
                                    }" x-init="initSearch(); $watch('generatorArea', val => { 
                                        const found = options.find(o => o.value === val); 
                                        if(found) search = found.label; 
                                    })">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Select Area</label>

                                <div class="relative">
                                    <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                        @click.away="open = false; revertSearch();" @input="open = true"
                                        @keydown.escape="open = false; revertSearch();"
                                        @keydown.enter.prevent="if(filteredOptions.length > 0) selectOption(filteredOptions[0])"
                                        placeholder="Search or select an area"
                                        class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all text-sm bg-white cursor-text"
                                        autocomplete="off">
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform"
                                        :class="open ? 'rotate-180' : ''"></i>

                                    <!-- Dropdown -->
                                    <div x-show="open" x-transition.opacity.duration.200ms
                                        class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto"
                                        style="display: none;">
                                        <ul class="py-1">
                                            <template x-for="option in filteredOptions" :key="option.value">
                                                <li class="px-4 py-2.5 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                                    :class="generatorArea === option.value ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
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
                        @else
                            <div class="p-3 bg-[#008080]/10 rounded-lg border border-[#008080]/20 text-[#008080] text-sm">
                                Generating QR for: <strong>{{ auth()->user()->dedicated_area }}</strong>
                            </div>
                        @endif
                        <div
                            class="flex justify-center p-4 bg-white border-2 border-dashed border-slate-200 rounded-xl">
                            <canvas id="site-qr-canvas"></canvas>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg text-xs text-slate-500 break-all text-center">
                            <span x-text="siteUrl"></span>
                        </div>
                        <button @click="printQR()"
                            class="w-full py-3 bg-[#008080] hover:bg-[#006666] text-white font-bold rounded-xl shadow-lg shadow-[#008080]/20">
                            <i class="fa-solid fa-print mr-2"></i> Download / Print Poster
                        </button>
                    </div>
                </div>
            </div>

            {{-- Toast Notification --}}
            <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="fixed bottom-5 right-5 z-[100] px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 min-w-[300px]"
                :class="toastType === 'success' ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-rose-600 text-white shadow-rose-500/20'"
                style="display: none;" x-cloak>
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <i class="fa-solid" :class="toastType === 'success' ? 'fa-check' : 'fa-triangle-exclamation'"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm" x-text="toastType === 'success' ? 'Success' : 'Error'"></h4>
                    <p class="text-xs text-white/90" x-text="toastMessage"></p>
                </div>
                <button @click="showToast = false" class="ml-auto text-white/60 hover:text-white">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>
    </main>

    {{-- Pass all PHP data to JS safely via JSON --}}
    <script type="application/json" id="dashboard-config">{!! json_encode([
    'logIndexUrl' => route('api.logs.index'),
    'logStoreUrl' => route('api.logs.store'),
    'visitorPassUrl' => rtrim(config('app.url'), '/') . '/pass',
    'dedicatedArea' => auth()->user()->dedicated_area ?? 'General',
    'isAdmin' => (bool) auth()->user()->is_admin,
    'generatorArea' => auth()->user()->is_admin ? 'General' : (auth()->user()->dedicated_area ?? 'General'),
], JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) !!}</script>

    {{-- Logic is in app.js / dashboard.js --}}
</body>

</html>

