<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PWA App') }} | Statistics</title>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
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
    class="antialiased bg-slate-50 text-slate-800 selection:bg-[#008080] selection:text-white h-screen overflow-y-auto flex flex-col lg:flex-row">

    @include('dashboard.partials.sidebar')

    {{-- Mobile Header --}}
    <div class="lg:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="flex items-center gap-2">
            <img src="{{ asset('hinatourist-logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
            <span
                class="font-black text-transparent bg-clip-text bg-[linear-gradient(to_bottom,#008080,#1A4B9F)]">{{ config('app.name') }}</span>
        </div>
        <button @click="sidebarOpen = true" class="p-2 transition-transform hover:scale-110">
            <i
                class="fa-solid fa-bars text-xl text-transparent bg-clip-text bg-[linear-gradient(to_bottom,#008080,#1A4B9F)]"></i>
        </button>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col relative overflow-y-auto min-w-0 px-4 pt-6 pb-6 sm:px-8 sm:pt-8 sm:pb-10">
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-[15%] right-[10%] w-[35%] h-[35%] rounded-full bg-indigo-100/40 blur-[100px]">
            </div>
            <div class="absolute bottom-[10%] left-[5%] w-[30%] h-[30%] rounded-full bg-cyan-100/30 blur-[80px]"></div>
        </div>

        <div class="w-full px-0 sm:px-2 pt-0 pb-4">
            {{-- Page Header --}}
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mb-1">Statistics</h1>
                    <p class="text-slate-500 text-sm">Comprehensive analytics across all tourist destinations.</p>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.export.var2p') }}" method="GET" class="flex items-center gap-3">
                        <div class="relative group"
                            x-data="{ openYear: false, selectedYear: '{{ request('year', max(now()->year, 2026)) }}' }">
                            <input type="hidden" name="year" :value="selectedYear">
                            <button type="button" @click="openYear = !openYear" @click.away="openYear = false"
                                class="flex items-center gap-2 pl-4 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:border-[#008080]/50 hover:bg-[#008080]/5 focus:ring-4 focus:ring-[#008080]/10 focus:border-[#008080] outline-none transition-all text-sm font-bold text-slate-700 shadow-sm cursor-pointer whitespace-nowrap">
                                <i
                                    class="fa-solid fa-calendar-day text-slate-400 group-hover:text-[#008080] transition-colors"></i>
                                <span x-text="selectedYear + ' Report'"></span>
                            </button>
                            <!-- Custom Dropdown -->
                            <div x-show="openYear" x-transition.opacity.duration.200ms
                                class="absolute right-0 mt-2 z-50 w-full min-w-[140px] bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto"
                                style="display: none;">
                                <ul class="py-1">
                                    @for($i = max(now()->year, 2026); $i >= 2026; $i--)
                                        <li class="px-4 py-2 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm font-semibold text-slate-700 transition-colors text-center"
                                            :class="selectedYear == '{{ $i }}' ? 'bg-[#008080]/10 text-[#008080] font-bold' : ''"
                                            @click="selectedYear = '{{ $i }}'; openYear = false; $nextTick(() => { $el.closest('form').submit(); })">
                                            {{ $i }} Report
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                        </div>
                        <button type="submit"
                            class="group px-5 py-2.5 rounded-xl bg-[#008080] hover:bg-[#006666] active:bg-[#005555] text-white font-semibold transition-all shadow-md shadow-[#008080]/30 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm flex items-center gap-2 text-sm whitespace-nowrap">
                            <i class="fa-solid fa-file-excel group-hover:scale-110 transition-transform"></i>
                            Download VAR 2P
                        </button>
                    </form>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 1: Summary Stat Cards (Live Polling) --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div x-data="liveStats()" x-init="init()" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

                {{-- Today --}}
                <div
                    class="bg-white/90 backdrop-blur p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-sun text-sm"></i>
                        </div>
                        <span
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-tight">Today</span>
                    </div>
                    <span class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300"
                        :class="flashing.today ? 'text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] scale-110' : ''"
                        x-text="fmt(stats.today)">{{ number_format($stats['today']) }}</span>
                    <span x-show="flashing.today" x-transition.opacity
                        class="absolute top-2 right-2 flex items-center gap-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 opacity-60"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30"></span>
                        </span>
                        <span
                            class="text-[9px] font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]">UPDATED</span>
                    </span>
                </div>

                {{-- This Month --}}
                <div
                    class="bg-white/90 backdrop-blur p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calendar-check text-sm"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-tight">This
                            Month</span>
                    </div>
                    <span class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300"
                        :class="flashing.month ? 'text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] scale-110' : ''"
                        x-text="fmt(stats.month)">{{ number_format($stats['month']) }}</span>
                    <span x-show="flashing.month" x-transition.opacity
                        class="absolute top-2 right-2 flex items-center gap-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 opacity-60"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30"></span>
                        </span>
                        <span
                            class="text-[9px] font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]">UPDATED</span>
                    </span>
                </div>

                {{-- Tourists --}}
                <div
                    class="bg-white/90 backdrop-blur p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-plane-arrival text-sm"></i>
                        </div>
                        <span
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-tight">Tourists</span>
                    </div>
                    <span class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300"
                        :class="flashing.tourist ? 'text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] scale-110' : ''"
                        x-text="fmt(stats.tourist)">{{ number_format($stats['tourist']) }}</span>
                    <span x-show="flashing.tourist" x-transition.opacity
                        class="absolute top-2 right-2 flex items-center gap-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 opacity-60"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30"></span>
                        </span>
                        <span
                            class="text-[9px] font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]">UPDATED</span>
                    </span>
                </div>

                {{-- Locals --}}
                <div
                    class="bg-white/90 backdrop-blur p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-house-user text-sm"></i>
                        </div>
                        <span
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-tight">Locals</span>
                    </div>
                    <span class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300"
                        :class="flashing.local ? 'text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] scale-110' : ''"
                        x-text="fmt(stats.local)">{{ number_format($stats['local']) }}</span>
                    <span x-show="flashing.local" x-transition.opacity
                        class="absolute top-2 right-2 flex items-center gap-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 opacity-60"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30"></span>
                        </span>
                        <span
                            class="text-[9px] font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]">UPDATED</span>
                    </span>
                </div>

                {{-- Male --}}
                <div
                    class="bg-white/90 backdrop-blur p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-mars text-sm"></i>
                        </div>
                        <span
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-tight">Male</span>
                    </div>
                    <span class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300"
                        :class="flashing.total_male ? 'text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] scale-110' : ''"
                        x-text="fmt(stats.total_male)">{{ number_format($stats['total_male']) }}</span>
                    <span x-show="flashing.total_male" x-transition.opacity
                        class="absolute top-2 right-2 flex items-center gap-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 opacity-60"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30"></span>
                        </span>
                        <span
                            class="text-[9px] font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]">UPDATED</span>
                    </span>
                </div>

                {{-- Female --}}
                <div
                    class="bg-white/90 backdrop-blur p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-venus text-sm"></i>
                        </div>
                        <span
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-tight">Female</span>
                    </div>
                    <span class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300"
                        :class="flashing.total_female ? 'text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] scale-110' : ''"
                        x-text="fmt(stats.total_female)">{{ number_format($stats['total_female']) }}</span>
                    <span x-show="flashing.total_female" x-transition.opacity
                        class="absolute top-2 right-2 flex items-center gap-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 opacity-60"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30"></span>
                        </span>
                        <span
                            class="text-[9px] font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]">UPDATED</span>
                    </span>
                </div>
            </div>

            <script>
                function liveStats() {
                    return {
                        stats: {
                            today:        {{ $stats['today'] }},
                            month:        {{ $stats['month'] }},
                            tourist:      {{ $stats['tourist'] }},
                            local:        {{ $stats['local'] }},
                            total_male:   {{ $stats['total_male'] }},
                            total_female: {{ $stats['total_female'] }},
                        },
                        // Plain object — Alpine can detect property mutations on this
                        flashing: { today: false, month: false, tourist: false, local: false, total_male: false, total_female: false },
                        _interval: null,

                        fmt(n) {
                            return Number(n).toLocaleString();
                        },

                        async poll() {
                            try {
                                const res = await fetch('/api/statistics/summary');
                                if (!res.ok) return;
                                const fresh = await res.json();
                                Object.keys(fresh).forEach(k => {
                                    if (fresh[k] !== this.stats[k]) {
                                        this.stats[k] = fresh[k];
                                        // Light up THIS card — Alpine watches the property change
                                        this.flashing[k] = true;
                                        setTimeout(() => { this.flashing[k] = false; }, 2000);
                                    }
                                });
                            } catch (e) { /* silently ignore network blips */ }
                        },

                        init() {
                            // Poll every 3 seconds for near-real-time updates
                            this._interval = setInterval(() => this.poll(), 3000);
                        },
                    };
                }
            </script>



            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 2: Monthly Trend Line Chart --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div class="bg-white/90 backdrop-blur p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Monthly Visitor Trend</h2>
                        <p class="text-sm text-slate-500">Monthly visitor counts monitor.</p>
                    </div>
                    <div
                        class="w-11 h-11 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="w-full" style="height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 3: Bar + Doughnut + Pie --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 mb-8">
                {{-- Bar Chart: Visitors per Spot --}}
                <div
                    class="xl:col-span-2 bg-white/90 backdrop-blur p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Visitors per Destination</h2>
                            <p class="text-sm text-slate-500">Total headcount per tourist spot.</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-inner">
                            <i class="fa-solid fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="w-full" style="height: 320px;">
                        <canvas id="spotsChart"></canvas>
                    </div>
                </div>

                {{-- Doughnut: Visit Reason --}}
                <div class="bg-white/90 backdrop-blur p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Visit Reasons</h2>
                            <p class="text-sm text-slate-500">Why visitors come.</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shadow-inner">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                    </div>
                    <div class="w-full flex justify-center" style="height: 260px;">
                        <canvas id="reasonChart"></canvas>
                    </div>
                    {{-- Legend --}}
                    <div class="mt-4 space-y-2 max-h-32 overflow-y-auto pr-2">
                        @foreach($reasonStats as $reason => $count)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600 truncate mr-2">{{ $reason }}</span>
                                <span class="font-bold text-slate-800">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pie: Origin --}}
                <div class="bg-white/90 backdrop-blur p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Origin</h2>
                            <p class="text-sm text-slate-500">Where are you from.</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg shadow-inner">
                            <i class="fa-solid fa-earth-americas"></i>
                        </div>
                    </div>
                    <div class="w-full flex justify-center" style="height: 260px;">
                        <canvas id="originChart"></canvas>
                    </div>
                    {{-- Legend --}}
                    <div class="mt-4 space-y-2 max-h-32 overflow-y-auto pr-2">
                        @foreach($originStats as $origin => $count)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600 truncate mr-2"
                                    title="{{ $origin }}">{{ $origin ?: 'Unknown' }}</span>
                                <span class="font-bold text-slate-800">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 4: Detailed Area Breakdown (Dynamic) --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div x-data="areaBreakdown()" x-init="init()"
                class="bg-white/90 backdrop-blur rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                {{-- ── Panel Header ── --}}
                <div class="p-5 sm:p-6 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-base shadow-inner shrink-0">
                                <i class="fa-solid fa-table-list"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-800 leading-tight">Detailed Area Breakdown
                                </h2>
                                <p class="text-xs text-slate-400 mt-0.5">Per-destination visitor statistics by day</p>
                            </div>
                        </div>

                        {{-- Sync Status Indicator --}}
                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <template x-if="lastSync">
                                <div
                                    class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
                                    <!-- <span class="relative flex h-2 w-2 shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span> -->
                                    <span class="text-[11px] font-medium text-slate-500 tracking-tight">Last logs sync:
                                        <span class="font-bold text-slate-700" x-text="lastSync"></span>
                                    </span>
                                </div>
                            </template>
                            <template x-if="!lastSync">
                                <div
                                    class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
                                    <span class="relative flex h-2 w-2 shrink-0">
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-300"></span>
                                    </span>
                                    <span class="text-[11px] font-medium text-slate-400 tracking-tight">No sync
                                        data</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- ── Primary nav: Year/Month Picker ── --}}
                    <div class="mt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Select Month</p>
                        <div class="flex items-center gap-2 flex-wrap">
                            {{-- Year stepper --}}
                            <div class="flex items-center gap-1 bg-slate-100 rounded-xl p-1 shrink-0">
                                <button @click="if(selectedYear > 2024){ selectedYear--; fetchDays(); }"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm transition-all text-xs"
                                    title="Previous year">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <span class="text-sm font-black text-slate-700 px-2 select-none"
                                    x-text="selectedYear"></span>
                                <button @click="if(selectedYear < currentYear){ selectedYear++; fetchDays(); }"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm transition-all text-xs"
                                    title="Next year">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>

                            {{-- Month buttons --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <template x-for="(mName, mIdx) in monthNames" :key="mIdx">
                                    <button @click="selectMonth(mIdx + 1)" :class="selectedMonth === (mIdx + 1)
                                            ? 'bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 text-white shadow-md shadow-[#008080]/30 ring-0'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 whitespace-nowrap"
                                        x-text="mName"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- ── Secondary nav: Day Picker ── --}}
                    <div x-show="availableDays.length > 0" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 pt-3 border-t border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                            Select Day —
                            <span class="normal-case font-medium text-slate-500"
                                x-text="monthNames[selectedMonth-1] + ' ' + selectedYear"></span>
                        </p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            {{-- "All Days" pill --}}
                            <button @click="selectDay(null)" :class="selectedDay === null
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'bg-slate-100 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-150 whitespace-nowrap">All</button>

                            <template x-for="d in availableDays" :key="d">
                                <button @click="selectDay(d)" :class="selectedDay === d
                                        ? 'bg-indigo-600 text-white shadow-sm'
                                        : 'bg-slate-100 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600'"
                                    class="w-9 h-8 rounded-lg text-xs font-semibold transition-all duration-150"
                                    x-text="d"></button>
                            </template>
                        </div>
                    </div>

                    {{-- Empty days notice --}}
                    <div x-show="!daysLoading && availableDays.length === 0" x-transition.opacity
                        class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-400 flex items-center gap-2">
                        <i class="fa-regular fa-calendar-xmark text-slate-300"></i>
                        No log entries found for <span class="font-semibold"
                            x-text="monthNames[selectedMonth-1] + ' ' + selectedYear"></span>.
                    </div>
                </div>

                {{-- ── Loading Skeleton ── --}}
                <div x-show="tableLoading" class="p-6 space-y-3" x-transition.opacity>
                    <template x-for="i in 5" :key="i">
                        <div class="flex items-center gap-4 animate-pulse">
                            <div class="w-8 h-8 rounded-full bg-slate-200 shrink-0"></div>
                            <div class="flex-1 h-4 bg-slate-100 rounded-lg"></div>
                            <div class="w-16 h-4 bg-slate-100 rounded-lg"></div>
                            <div class="w-16 h-4 bg-slate-100 rounded-lg"></div>
                            <div class="w-16 h-4 bg-slate-100 rounded-lg"></div>
                            <div class="w-16 h-4 bg-slate-100 rounded-lg"></div>
                        </div>
                    </template>
                </div>

                {{-- ── Data Table ── --}}
                <div x-show="!tableLoading" x-transition.opacity>
                    {{-- Context label --}}
                    <div class="px-5 sm:px-6 pt-4 pb-1 flex items-center justify-between flex-wrap gap-2">
                        <p class="text-xs font-semibold text-slate-500">
                            Showing results for
                            <span class="text-slate-800 font-bold" x-text="selectedDay
                                    ? monthNames[selectedMonth-1] + ' ' + selectedDay + ', ' + selectedYear + ' (24-hr window)'
                                    : monthNames[selectedMonth-1] + ' ' + selectedYear + ' (Full month)'"></span>
                        </p>
                        <span
                            class="text-[11px] font-bold text-teal-600 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-full"
                            x-text="tableRows.length + ' spot' + (tableRows.length !== 1 ? 's' : '')"></span>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="lg:hidden divide-y divide-slate-100 mt-2">
                        <template x-if="tableRows.length > 0">
                            <template x-for="row in tableRows" :key="row.dedicated_area">
                                <div class="p-4">
                                    <p class="font-bold text-slate-900 mb-2" x-text="row.dedicated_area"></p>
                                    <div class="grid grid-cols-3 gap-2 text-sm">
                                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                                            <span
                                                class="block text-[10px] text-slate-400 uppercase font-bold">Total</span>
                                            <span class="font-black text-slate-800"
                                                x-text="Intl.NumberFormat().format(row.total_visitors)"></span>
                                        </div>
                                        <div class="bg-sky-50 rounded-lg p-2 text-center">
                                            <span
                                                class="block text-[10px] text-sky-400 uppercase font-bold">Tourists</span>
                                            <span class="font-black text-sky-700"
                                                x-text="Intl.NumberFormat().format(row.tourists)"></span>
                                        </div>
                                        <div class="bg-rose-50 rounded-lg p-2 text-center">
                                            <span
                                                class="block text-[10px] text-rose-400 uppercase font-bold">Locals</span>
                                            <span class="font-black text-rose-700"
                                                x-text="Intl.NumberFormat().format(row.locals)"></span>
                                        </div>
                                        <div class="bg-blue-50 rounded-lg p-2 text-center">
                                            <span
                                                class="block text-[10px] text-blue-400 uppercase font-bold">Male</span>
                                            <span class="font-black text-blue-700"
                                                x-text="Intl.NumberFormat().format(row.males)"></span>
                                        </div>
                                        <div class="bg-pink-50 rounded-lg p-2 text-center">
                                            <span
                                                class="block text-[10px] text-pink-400 uppercase font-bold">Female</span>
                                            <span class="font-black text-pink-700"
                                                x-text="Intl.NumberFormat().format(row.females)"></span>
                                        </div>
                                        <div class="bg-emerald-50 rounded-lg p-2 text-center">
                                            <span
                                                class="block text-[10px] text-emerald-400 uppercase font-bold">Entries</span>
                                            <span class="font-black text-emerald-700"
                                                x-text="Intl.NumberFormat().format(row.total_entries)"></span>
                                            <span x-show="row.spot_last_sync"
                                                class="block text-[9px] text-slate-400 font-normal mt-0.5"
                                                x-text="'Last sync: ' + row.spot_last_sync"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <template x-if="tableRows.length === 0">
                            <div class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-chart-bar text-3xl opacity-20 mb-2 block"></i>
                                No visitor data for this period.
                            </div>
                        </template>
                    </div>

                    {{-- Desktop table --}}
                    <div class="hidden lg:block overflow-x-auto mt-2">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                    <th class="px-5 py-3 font-semibold">Tourist Spot</th>
                                    <th class="px-5 py-3 font-semibold text-center">Total</th>
                                    <th class="px-5 py-3 font-semibold text-center">Tourists</th>
                                    <th class="px-5 py-3 font-semibold text-center">Locals</th>
                                    <th class="px-5 py-3 font-semibold text-center">Male</th>
                                    <th class="px-5 py-3 font-semibold text-center">Female</th>
                                    <th
                                        class="px-5 py-3 font-semibold text-center uppercase tracking-wider text-[11px]">
                                        Log Entries</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-if="tableRows.length > 0">
                                    <template x-for="(row, idx) in tableRows" :key="row.dedicated_area">
                                        <tr class="hover:bg-slate-50/60 transition-colors group">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center shrink-0 text-xs font-bold transition-transform group-hover:scale-110">
                                                        <i class="fa-solid fa-location-dot"></i>
                                                    </div>
                                                    <span class="font-semibold text-slate-800 text-sm"
                                                        x-text="row.dedicated_area"></span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="font-black text-slate-800 text-sm"
                                                    x-text="Intl.NumberFormat().format(row.total_visitors)"></span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span
                                                    class="inline-block bg-sky-50 text-sky-700 font-bold text-xs px-2.5 py-1 rounded-full"
                                                    x-text="Intl.NumberFormat().format(row.tourists)"></span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span
                                                    class="inline-block bg-rose-50 text-rose-700 font-bold text-xs px-2.5 py-1 rounded-full"
                                                    x-text="Intl.NumberFormat().format(row.locals)"></span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center text-blue-600 font-semibold text-sm"
                                                x-text="Intl.NumberFormat().format(row.males)"></td>
                                            <td class="px-5 py-3.5 text-center text-pink-600 font-semibold text-sm"
                                                x-text="Intl.NumberFormat().format(row.females)"></td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="block font-bold text-slate-700 text-sm"
                                                    x-text="Intl.NumberFormat().format(row.total_entries)"></span>
                                                <span x-show="row.spot_last_sync"
                                                    class="block text-[10px] text-slate-400 font-medium mt-1 tracking-tight"
                                                    x-text="'(Last sync: ' + row.spot_last_sync + ')'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="tableRows.length === 0">
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            <i class="fa-solid fa-calendar-xmark text-3xl opacity-20 mb-3 block"></i>
                                            <p class="text-sm">No visitor data for this period.</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 5: Forecast --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div x-data="forecastData()" x-init="initForecasts()"
                class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-3xl shadow-xl shadow-[#008080]/30 border border-slate-800 overflow-hidden mb-8 text-white relative">
                <!-- Decorative AI background -->
                <div
                    class="absolute -top-24 -right-24 w-64 h-64 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 rounded-full blur-[80px] opacity-20 pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-24 -left-24 w-64 h-64 bg-violet-600 rounded-full blur-[80px] opacity-20 pointer-events-none">
                </div>

                <div
                    class="p-6 sm:p-8 border-b border-white/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-sparkles text-amber-400"></i>Forecast
                        </h2>
                        <p class="text-sm text-slate-400 mt-1">SARIMA
                            model.</p>
                    </div>

                    <!-- Month Selector -->
                    <div x-show="!loading && !error" x-cloak class="relative w-full sm:w-auto">
                        <select x-model="selectedMonth"
                            class="w-full sm:w-auto bg-white/10 border border-white/20 text-white text-sm rounded-xl px-4 py-2 hover:bg-white/20 focus:ring-2 focus:ring-indigo-500 focus:outline-none appearance-none pr-10 cursor-pointer transition-colors backdrop-blur-sm">
                            <option value="all" class="text-slate-900">Total (Next 12 Months)</option>
                            <template x-for="month in availableMonths" :key="month.value">
                                <option :value="month.value" x-text="month.label" class="text-slate-900"></option>
                            </template>
                        </select>
                        <i
                            class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-white/50 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div class="p-6 sm:p-8 relative z-10">
                    <!-- Loading State -->
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                        <i
                            class="fa-solid fa-circle-notch fa-spin text-4xl text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] mb-4"></i>
                        <p class="text-slate-300 font-medium animate-pulse">Running advanced machine learning models...
                        </p>
                    </div>

                    <!-- Offline / Error State -->
                    <div x-show="error" x-cloak
                        class="bg-white/5 border border-white/10 rounded-3xl p-8 text-center backdrop-blur-md flex flex-col items-center justify-center max-w-2xl mx-auto">
                        <div
                            class="w-16 h-16 rounded-full bg-slate-800/80 flex items-center justify-center text-slate-400 text-2xl mb-5 shadow-inner border border-white/5">
                            <i class="fa-solid fa-server"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2"
                            x-text="error === 'Offline mode.' ? 'You are currently offline' : 'Forecasting Engine Unavailable'">
                        </h3>
                        <p class="text-slate-400 text-sm mb-6 leading-relaxed"
                            x-text="error === 'Offline mode.' ? 'Connect to the internet to sync and pull the latest AI predictions.' : 'The SARIMA machine learning API stream could not be reached. Ensure the FastAPI backend is running locally on port 8000.'">
                        </p>

                        <button @click="initForecasts()"
                            class="px-5 py-2.5 bg-[#008080]/20 hover:bg-[#008080]/40 border border-indigo-500/50 text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080] rounded-xl text-sm font-bold transition-all focus:ring-2 focus:ring-indigo-500">
                            <i class="fa-solid fa-rotate-right mr-2"></i> Try Refreshing
                        </button>
                    </div>

                    <!-- Forecast List -->
                    <div x-show="!loading && !error" x-cloak
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="(spot, index) in rankedForecasts" :key="spot.name">
                            <div @click="openSpotModal(spot, index)"
                                class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 transition-colors group relative overflow-hidden cursor-pointer">
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110 pointer-events-none">
                                </div>

                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold text-lg"
                                        :class="index === 0 ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : (index === 1 ? 'bg-slate-300 text-slate-800' : (index === 2 ? 'bg-amber-700 text-white' : 'bg-indigo-500/20 text-indigo-300'))">
                                        <span x-text="'#' + (index + 1)"></span>
                                    </div>
                                    <h3 class="font-bold text-sm text-slate-200 leading-snug pr-2"
                                        x-text="spot.name_formatted"></h3>
                                </div>
                                <div class="flex items-center gap-1.5 mb-2 flex-wrap">
                                    <template x-if="spot.isFallback">
                                        <span
                                            class="text-[9px] font-black px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center gap-1">
                                            <i class="fa-solid fa-shield-halved"></i> FALLBACK ACTIVE
                                        </span>
                                    </template>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                        :class="getMapeColor(spot.mape)" x-text="'MAPE ' + spot.mape + '%'"></span>
                                    <span class="text-[10px] text-slate-500"
                                        x-text="'· ' + spot.mape_interpretation"></span>
                                </div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-white"
                                        x-text="Intl.NumberFormat().format(spot.total_predicted)"></span>
                                    <span
                                        class="text-xs font-medium text-slate-400 uppercase tracking-wider">Expected</span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-1.5 text-[10px] text-slate-400">
                                    <i class="fa-solid fa-arrow-down-up-across-line text-[#008080]/70 text-[8px]"></i>
                                    <span>95% CI:</span>
                                    <span class="text-slate-300 font-semibold" x-text="spot.confidence_text"></span>
                                </div>
                                <!-- Action Recommendation based on Rank -->
                                <div class="mt-4 pt-4 border-t border-white/10 text-xs text-slate-300 leading-relaxed">
                                    <!-- Rank 1-2 -->
                                    <div x-show="index <= 1" class="flex items-start gap-2">
                                        <i class="fa-solid fa-circle-exclamation text-rose-400 mt-0.5"></i>
                                        <span><strong>Action:</strong> Prioritize deployment of personnel and safety
                                            officers. Ensure maximum operational readiness of facilities.</span>
                                    </div>

                                    <!-- Rank 3-5 -->
                                    <div x-show="index >= 2 && index <= 4" class="flex items-start gap-2">
                                        <i class="fa-solid fa-clipboard-check text-amber-400 mt-0.5"></i>
                                        <span><strong>Action:</strong> Initiate routine maintenance and facility
                                            repairs. Focus on sustainability and waste management efficiency.</span>
                                    </div>

                                    <!-- Rank 6-8 -->
                                    <div x-show="index >= 5 && index <= 7" class="flex items-start gap-2">
                                        <i class="fa-solid fa-bullhorn text-sky-400 mt-0.5"></i>
                                        <span><strong>Action:</strong> Enhance digital visibility through LGU-led
                                            promotions. Analyze data to identify potential growth opportunities.</span>
                                    </div>
                                </div>
                                <!-- Click hint -->
                                <div
                                    class="mt-3 text-[10px] text-slate-500 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-up-right-and-down-left-from-center"></i> Click for details
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ═══ Forecasting Insights Panel ═══ --}}
                    <div x-show="!loading && !error && forecasts.length > 0" x-cloak class="mt-8">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-violet-500/20 text-violet-400 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-white">Forecasting Insights</h3>
                                    <p class="text-xs text-slate-400">Detailed analysis per destination</p>
                                </div>
                            </div>
                            <div class="relative flex-1 sm:flex-initial">
                                <select x-model="selectedSpotIndex" @change="$nextTick(() => buildForecastChart())"
                                    class="w-full sm:w-auto bg-white/10 border border-white/20 text-white text-xs rounded-xl px-4 py-2.5 hover:bg-white/20 focus:ring-2 focus:ring-violet-500 focus:outline-none appearance-none pr-10 cursor-pointer transition-colors backdrop-blur-sm font-semibold">
                                    <template x-for="(spot, idx) in rankedForecasts" :key="idx">
                                        <option :value="idx" x-text="spot.name_formatted" class="text-slate-900">
                                        </option>
                                    </template>
                                </select>
                                <i
                                    class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-white/40 text-[10px] pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex gap-1 mb-6 bg-white/5 rounded-xl p-1 border border-white/10 w-fit">
                            <button @click="activeInsightsTab = 'chart'; $nextTick(() => buildForecastChart())"
                                :class="activeInsightsTab === 'chart' ? 'bg-[#008080]/30 text-white border-indigo-400/50' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all border">
                                <i class="fa-solid fa-chart-area mr-1.5"></i>Forecast Chart
                            </button>
                            <button @click="activeInsightsTab = 'seasonal'"
                                :class="activeInsightsTab === 'seasonal' ? 'bg-emerald-500/30 text-white border-emerald-400/50' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all border">
                                <i class="fa-solid fa-leaf mr-1.5"></i>Seasonal Patterns
                            </button>
                            <button @click="activeInsightsTab = 'peaks'"
                                :class="activeInsightsTab === 'peaks' ? 'bg-amber-500/30 text-white border-amber-400/50' : 'text-slate-400 hover:text-slate-200 border-transparent'"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all border">
                                <i class="fa-solid fa-fire mr-1.5"></i>Peak Seasons
                            </button>
                        </div>

                        {{-- Chart Tab --}}
                        <div x-show="activeInsightsTab === 'chart'"
                            x-transition:enter="transition ease-out duration-500 delay-100"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bg-white/5 border border-white/10 rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-bold text-white">Actual vs Predicted Comparison</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Historical actual data compared against
                                        model predictions</p>
                                </div>
                                <div class="flex items-center gap-4 text-[10px] text-slate-400 flex-wrap">
                                    <span class="flex items-center gap-1.5"><span
                                            class="w-3 h-0.5 bg-[#F59E0B] rounded-full inline-block"></span> Actual
                                        Data</span>
                                    <span class="flex items-center gap-1.5"><span
                                            class="w-3 h-0.5 bg-[#4ADE80] rounded-full inline-block"
                                            style="border-bottom: 2px dashed #4ADE80;"></span> Predicted</span>
                                    <span class="flex items-center gap-1.5"><span
                                            class="w-3 h-3 bg-[#4ADE80]/20 rounded inline-block border border-emerald-400/30"></span>
                                        95% CI</span>
                                </div>
                            </div>
                            <div style="height: 300px;"><canvas id="forecastInsightChart"></canvas></div>
                        </div>

                        {{-- Seasonal Patterns Tab --}}
                        <div x-show="activeInsightsTab === 'seasonal'"
                            x-transition:enter="transition ease-out duration-500 delay-100"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bg-white/5 border border-white/10 rounded-2xl p-5">
                            <h4 class="text-sm font-bold text-white mb-4">Monthly Seasonal Classification</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                <template x-for="item in (rankedForecasts[selectedSpotIndex]?.seasonal_analysis || [])"
                                    :key="item.month">
                                    <div class="rounded-xl p-3 border transition-all"
                                        :class="item.classification === 'Peak Season' ? 'bg-emerald-500/15 border-emerald-500/30' : (item.classification === 'Regular Season' ? 'bg-amber-500/10 border-amber-500/20' : 'bg-slate-500/10 border-slate-500/20')">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-white" x-text="item.month"></span>
                                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full"
                                                :class="item.classification === 'Peak Season' ? 'bg-emerald-500/30 text-emerald-300' : (item.classification === 'Regular Season' ? 'bg-amber-500/30 text-amber-300' : 'bg-slate-500/30 text-slate-400')"
                                                x-text="item.classification"></span>
                                        </div>
                                        <div class="text-lg font-black text-white"
                                            x-text="Intl.NumberFormat().format(getPredictedForMonth(item.month, rankedForecasts[selectedSpotIndex]))">
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1 leading-snug"
                                            x-text="item.associated_holidays"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Peak Seasons Tab --}}
                        <div x-show="activeInsightsTab === 'peaks'"
                            x-transition:enter="transition ease-out duration-500 delay-100"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bg-white/5 border border-white/10 rounded-2xl p-5">
                            <h4 class="text-sm font-bold text-white mb-4">Top 3 Peak Months</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <template x-for="peak in (rankedForecasts[selectedSpotIndex]?.peak_seasons || [])"
                                    :key="peak.rank">
                                    <div class="relative rounded-2xl p-5 border overflow-hidden"
                                        :class="peak.rank === 1 ? 'bg-gradient-to-br from-amber-500/20 to-amber-600/10 border-amber-500/30' : (peak.rank === 2 ? 'bg-gradient-to-br from-slate-400/10 to-slate-500/5 border-slate-400/20' : 'bg-gradient-to-br from-amber-700/15 to-amber-800/5 border-amber-700/20')">
                                        <div class="absolute top-3 right-3 w-10 h-10 rounded-full flex items-center justify-center font-black text-lg"
                                            :class="peak.rank === 1 ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : (peak.rank === 2 ? 'bg-slate-300 text-slate-800' : 'bg-amber-700 text-white')">
                                            <span x-text="'#' + peak.rank"></span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-xs text-slate-400 uppercase tracking-wider font-bold">Peak
                                                Month</span>
                                            <h5 class="text-xl font-black text-white mt-1" x-text="peak.month"></h5>
                                        </div>
                                        <div class="text-2xl font-black text-white mb-1"
                                            x-text="Intl.NumberFormat().format(getPredictedForMonth(peak.month, rankedForecasts[selectedSpotIndex]))">
                                        </div>
                                        <span
                                            class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Expected
                                            Visitors</span>
                                        <div class="mt-3 pt-3 border-t border-white/10">
                                            <div class="flex items-start gap-2">
                                                <i class="fa-solid fa-calendar-star text-amber-400 text-xs mt-0.5"></i>
                                                <span class="text-xs text-slate-300 leading-relaxed"
                                                    x-text="peak.associated_holidays"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Forecast Detail Modal --}}
                <div x-show="showSpotModal" x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    @click.self="showSpotModal = false">
                    <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-3xl shadow-2xl border border-white/10 w-full max-w-lg relative overflow-hidden"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                        {{-- Decorative blobs --}}
                        <div
                            class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-br from-[#008080] to-[#008080] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#008080]/30 rounded-full blur-[60px] opacity-20 pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-16 -left-16 w-40 h-40 bg-violet-600 rounded-full blur-[60px] opacity-20 pointer-events-none">
                        </div>

                        {{-- Modal Header --}}
                        <div class="p-6 pb-0 relative z-10">
                            <button @click="showSpotModal = false"
                                class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>

                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 font-black text-xl"
                                    :class="modalSpot.rank === 1 ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : (modalSpot.rank === 2 ? 'bg-slate-300 text-slate-800' : (modalSpot.rank === 3 ? 'bg-amber-700 text-white' : 'bg-[#008080]/20 text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]'))">
                                    <span x-text="'#' + modalSpot.rank"></span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white" x-text="modalSpot.name"></h3>
                                    <p class="text-sm text-slate-400">AI Demand Forecast</p>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 pt-0 relative z-10 space-y-4 max-h-[60vh] overflow-y-auto">
                            {{-- MAPE Accuracy --}}
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                                            Model Accuracy (MAPE)</p>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-3xl font-black text-white"
                                                x-text="modalSpot.mape + '%'"></span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                                                :class="getMapeColor(modalSpot.mape)"
                                                x-text="modalSpot.mape_interpretation"></span>
                                        </div>
                                    </div>
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl"
                                        :class="modalSpot.mape <= 10 ? 'bg-emerald-500/20 text-emerald-400' : (modalSpot.mape <= 20 ? 'bg-sky-500/20 text-sky-400' : (modalSpot.mape <= 50 ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400'))">
                                        <i class="fa-solid fa-bullseye"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Predicted + Confidence Interval --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                                        Expected Visitors</p>
                                    <span class="text-3xl font-black text-white"
                                        x-text="Intl.NumberFormat().format(modalSpot.predicted)"></span>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">95%
                                        Confidence Interval</p>
                                    <span
                                        class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-br from-[#008080] to-[#008080]"
                                        x-text="modalSpot.confidence_text"></span>
                                </div>
                            </div>

                            {{-- Seasonal Mini Heatmap --}}
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Season
                                    Overview</p>
                                <div class="grid grid-cols-6 gap-1.5">
                                    <template x-for="item in (modalSpot.seasonal_analysis || [])" :key="item.month">
                                        <div class="rounded-lg p-2 text-center"
                                            :class="item.classification === 'Peak Season' ? 'bg-emerald-500/20' : (item.classification === 'Regular Season' ? 'bg-amber-500/15' : 'bg-slate-500/15')">
                                            <span class="text-[9px] font-bold text-white block"
                                                x-text="item.month.substring(0, 3)"></span>
                                            <span class="text-[8px] mt-0.5 block"
                                                :class="item.classification === 'Peak Season' ? 'text-emerald-400' : (item.classification === 'Regular Season' ? 'text-amber-400' : 'text-slate-500')"
                                                x-text="item.classification === 'Peak Season' ? '▲ Peak' : (item.classification === 'Regular Season' ? '● Regular' : '▼ Low')"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Top Peak Months --}}
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Top Peak
                                    Months</p>
                                <div class="space-y-2">
                                    <template x-for="peak in (modalSpot.peak_seasons || [])" :key="peak.rank">
                                        <div class="flex items-center gap-3 bg-white/5 rounded-xl p-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                                :class="peak.rank === 1 ? 'bg-amber-500 text-white' : (peak.rank === 2 ? 'bg-slate-300 text-slate-800' : 'bg-amber-700 text-white')">
                                                <span x-text="'#' + peak.rank"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-bold text-white" x-text="peak.month"></span>
                                                <span class="text-xs text-slate-400 ml-2"
                                                    x-text="Intl.NumberFormat().format(getPredictedForMonth(peak.month, modalSpot.fullData)) + ' expected'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Action Recommendation --}}
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="text-lg" :class="modalSpot.actionIcon"></i>
                                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Recommended Action
                                    </h4>
                                </div>
                                <p class="text-sm text-slate-200 leading-relaxed" x-text="modalSpot.actionText"></p>
                            </div>

                            {{-- Priority Level --}}
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Priority
                                    Level:</span>
                                <span class="text-xs font-bold px-3 py-1 rounded-full"
                                    :class="modalSpot.rank <= 2 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : (modalSpot.rank <= 5 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-sky-500/20 text-sky-400 border border-sky-500/30')"
                                    x-text="modalSpot.rank <= 2 ? 'HIGH — Critical' : (modalSpot.rank <= 5 ? 'MEDIUM — Moderate' : 'LOW — Growth Opportunity')"></span>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="p-6 pt-2 relative z-10">
                            <button @click="showSpotModal = false"
                                class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-bold rounded-xl transition-all text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- Alpine Data for Forecast Component --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <script>
        function forecastData() {
            return {
                loading: true,
                error: null,
                forecasts: [],
                selectedMonth: 'all',
                availableMonths: [],
                showSpotModal: false,
                modalSpot: { name: '', rank: 0, predicted: 0, actionText: '', actionIcon: '', mape: 0, mape_interpretation: '', confidence_text: '', seasonal_analysis: [], peak_seasons: [] },
                selectedSpotIndex: 0,
                activeInsightsTab: 'chart',
                forecastChartInstance: null,
                fallbackActive: false, // UI flag for the whole system
                // Fallback list — used only if /attractions endpoint is unreachable
                fallbackSpots: [
                    'ENCHANTED RIVER',
                    'LODESTONE SHORES RESORT',
                    'BACULIN AMAZING SAND BAR',
                    'HARIP OCEANSIDE WHITE BEACH',
                    'ROCK ISLAND RESORT',
                    'AMPARITAS INTEGRATED NATURE FARM',
                    'SIBADAN FISH CAGE AND RESORT',
                    'DAVINCE HIDDEN PARADISE'
                ],

                getMapeColor(mape) {
                    if (mape <= 10) return 'bg-emerald-500/30 text-emerald-300';
                    if (mape <= 20) return 'bg-sky-500/30 text-sky-300';
                    if (mape <= 50) return 'bg-amber-500/30 text-amber-300';
                    return 'bg-rose-500/30 text-rose-300';
                },

                getActionForIndex(index) {
                    if (index <= 1) {
                        return {
                            text: 'Prioritize deployment of personnel and safety officers. Ensure maximum operational readiness of facilities.',
                            icon: 'fa-solid fa-circle-exclamation text-rose-400'
                        };
                    } else if (index <= 4) {
                        return {
                            text: 'Initiate routine maintenance and facility repairs. Focus on sustainability and waste management efficiency.',
                            icon: 'fa-solid fa-clipboard-check text-amber-400'
                        };
                    } else {
                        return {
                            text: 'Enhance digital visibility through LGU-led promotions. Analyze data to identify potential growth opportunities.',
                            icon: 'fa-solid fa-bullhorn text-sky-400'
                        };
                    }
                },

                openSpotModal(spot, index) {
                    const action = this.getActionForIndex(index);
                    this.modalSpot = {
                        name: spot.name_formatted, rank: index + 1, predicted: spot.total_predicted,
                        actionText: action.text, actionIcon: action.icon,
                        mape: spot.mape || 0, mape_interpretation: spot.mape_interpretation || '',
                        sarima_order: spot.sarima_order || 'SARIMA(1,1,1)x(1,1,0)12',
                        confidence_text: spot.confidence_text || '',
                        seasonal_analysis: spot.seasonal_analysis || [], peak_seasons: spot.peak_seasons || [],
                        historical_yearly: spot.historical_yearly || [],
                        fullData: spot,
                        historical_monthly: spot.historical_monthly || [],
                        historical_test: spot.historical_test || [],
                        test_predicted: spot.test_predicted || []
                    };
                    this.showSpotModal = true;
                },

                get rankedForecasts() {
                    return this.forecasts.map(spot => {
                        let total = 0, ciLower = 0, ciUpper = 0;
                        if (this.selectedMonth === 'all') {
                            spot.raw_forecasts.forEach(item => {
                                total += item.predicted_visitors;
                                ciLower += item.confidence_interval_lower;
                                ciUpper += item.confidence_interval_upper;
                            });
                        } else {
                            const md = spot.raw_forecasts.find(m => m.month === this.selectedMonth);
                            if (md) { total = md.predicted_visitors; ciLower = md.confidence_interval_lower; ciUpper = md.confidence_interval_upper; }
                        }
                        return {
                            ...spot, total_predicted: total, ci_lower: ciLower, ci_upper: ciUpper,
                            confidence_text: Intl.NumberFormat().format(ciLower) + ' – ' + Intl.NumberFormat().format(ciUpper)
                        };
                    }).sort((a, b) => b.total_predicted - a.total_predicted);
                },

                formatMonth(monthStr) {
                    const date = new Date(monthStr + '-01');
                    return new Intl.DateTimeFormat('en-US', { month: 'long' }).format(date);
                },

                getPredictedForMonth(monthName, spotData) {
                    if (!spotData || !spotData.raw_forecasts) return 0;
                    const monthMap = { 'January': 1, 'February': 2, 'March': 3, 'April': 4, 'May': 5, 'June': 6, 'July': 7, 'August': 8, 'September': 9, 'October': 10, 'November': 11, 'December': 12 };
                    const mNum = monthMap[monthName];
                    if (!mNum) return 0;
                    const found = spotData.raw_forecasts.find(f => {
                        const d = new Date(f.month + (f.month.length === 7 ? '-01' : ''));
                        return (d.getMonth() + 1) === mNum;
                    });
                    return found ? found.predicted_visitors : 0;
                },

                calculateSeasonalMean(historicalData) {
                    if (!historicalData || historicalData.length === 0) return [];

                    // Group by month (0-11)
                    const monthlyGroups = {};
                    historicalData.forEach(h => {
                        const d = new Date(h.month);
                        const m = d.getMonth();
                        if (!monthlyGroups[m]) monthlyGroups[m] = [];
                        monthlyGroups[m].push(h.visitors);
                    });

                    // Forecast for the next 12 months starting from next month
                    const results = [];
                    const now = new Date();
                    for (let i = 1; i <= 12; i++) {
                        const targetDate = new Date(now.getFullYear(), now.getMonth() + i, 1);
                        const m = targetDate.getMonth();
                        const year = targetDate.getFullYear();
                        const monthStr = `${year}-${String(m + 1).padStart(2, '0')}`;

                        const values = monthlyGroups[m] || [];
                        const mean = values.length > 0
                            ? Math.round(values.reduce((a, b) => a + b, 0) / values.length)
                            : 0;

                        results.push({
                            month: monthStr,
                            predicted_visitors: mean,
                            confidence_interval_lower: Math.max(0, Math.round(mean * 0.8)),
                            confidence_interval_upper: Math.round(mean * 1.2)
                        });
                    }
                    return results;
                },

                buildForecastChart() {
                    const canvas = document.getElementById('forecastInsightChart');
                    if (!canvas) return;
                    if (this.forecastChartInstance) this.forecastChartInstance.destroy();
                    const spot = this.rankedForecasts[this.selectedSpotIndex];
                    if (!spot) return;

                    let labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    let actuals = new Array(12).fill(null);
                    let predicted = new Array(12).fill(null);
                    let lower = new Array(12).fill(null);
                    let upper = new Array(12).fill(null);

                    // 1. Plot Actual Data (strictly 2025)
                    if (spot.historical_monthly) {
                        spot.historical_monthly.forEach(h => {
                            const d = new Date(h.month);
                            if (d.getFullYear() === 2025) {
                                actuals[d.getMonth()] = h.visitors;
                            }
                        });
                    }
                    if (spot.historical_test && spot.historical_test.length > 0) {
                        spot.historical_test.forEach(h => {
                            const d = new Date(h.month);
                            if (d.getFullYear() === 2025) {
                                actuals[d.getMonth()] = h.visitors;
                            }
                        });
                    }

                    // 2. Plot Predicted Data (Jan-Dec of forecast)
                    if (spot.raw_forecasts) {
                        spot.raw_forecasts.forEach(f => {
                            const d = new Date(f.month + '-01');
                            const monthIdx = d.getMonth();
                            predicted[monthIdx] = f.predicted_visitors;
                            lower[monthIdx] = f.confidence_interval_lower;
                            upper[monthIdx] = f.confidence_interval_upper;
                        });
                    }

                    this.forecastChartInstance = new Chart(canvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                // 95% CI shading (upper → lower fill)
                                { label: 'Upper Bound', data: upper, borderColor: 'rgba(74,222,128,0.25)', backgroundColor: 'rgba(74,222,128,0.10)', fill: '+1', pointRadius: 0, tension: 0.4, borderWidth: 1, borderDash: [4, 4], order: 3, spanGaps: true },
                                { label: 'Lower Bound', data: lower, borderColor: 'rgba(74,222,128,0.25)', backgroundColor: 'transparent', fill: false, pointRadius: 0, tension: 0.4, borderWidth: 1, borderDash: [4, 4], order: 4, spanGaps: true },
                                // Predicted line (green dashed)
                                { label: 'Predicted', data: predicted, borderColor: '#4ADE80', backgroundColor: 'rgba(74,222,128,0.05)', borderWidth: 3, pointRadius: 5, pointHoverRadius: 8, pointBackgroundColor: '#16A34A', pointBorderColor: '#4ADE80', pointBorderWidth: 2, tension: 0.4, fill: false, order: 1, spanGaps: true, borderDash: [6, 4] },
                                // Actual line (orange solid)
                                { label: 'Actual Data', data: actuals, borderColor: '#F59E0B', backgroundColor: 'rgba(245,158,11,0.08)', borderWidth: 3, pointRadius: 5, pointHoverRadius: 8, pointBackgroundColor: '#B45309', pointBorderColor: '#F59E0B', pointBorderWidth: 2, tension: 0.4, fill: true, order: 2, spanGaps: true },
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15,23,42,0.95)', titleFont: { family: 'Inter', size: 12 }, bodyFont: { family: 'Inter', size: 12 }, padding: 12, cornerRadius: 10,
                                    callbacks: {
                                        label: function (c) {
                                            if (c.raw === null || c.raw === undefined) return null;
                                            const v = Intl.NumberFormat().format(c.raw);
                                            if (c.datasetIndex === 3) return '🟠 Actual: ' + v;
                                            if (c.datasetIndex === 2) return '🟢 Predicted: ' + v;
                                            if (c.datasetIndex === 0) return '▲ Upper 95% CI: ' + v;
                                            if (c.datasetIndex === 1) return '▼ Lower 95% CI: ' + v;
                                            return v;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: false, grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { color: 'rgba(255,255,255,0.4)', font: { family: 'Inter', size: 11 }, callback: function (val) { return Intl.NumberFormat('en', { notation: 'compact' }).format(val); } } },
                                x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.4)', font: { family: 'Inter', size: 11 }, maxRotation: 45, minRotation: 45 } }
                            }
                        }
                    });
                },

                async initForecasts() {
                    if (!navigator.onLine) {
                        this.loading = false;
                        this.error = "Offline mode.";
                        return;
                    }

                    this.loading = true;
                    this.error = null;
                    this.forecasts = [];
                    this.availableMonths = [];

                    try {
                        // ── Step 1: Discover all trained models from the API ──
                        let spotNames = [];
                        try {
                            const attractionsRes = await fetch('/api/sarima/attractions');
                            if (attractionsRes.ok) {
                                const attractionsData = await attractionsRes.json();
                                spotNames = (attractionsData.attractions || []).map(a => a.name);
                                console.log(`Discovered ${spotNames.length} trained models from API:`, spotNames);
                            }
                        } catch (discoverErr) {
                            console.warn('Could not discover attractions, falling back to hardcoded list:', discoverErr);
                        }

                        // Fall back to hardcoded list if discovery failed
                        if (spotNames.length === 0) {
                            spotNames = this.fallbackSpots;
                            console.log('Using fallback spot list:', spotNames);
                        }

                        // ── Step 2: Fetch forecasts for every discovered attraction ──
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const requests = spotNames.map(async spotName => {
                            const response = await fetch('/api/sarima/forecast', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    attraction_name: spotName,
                                    months_ahead: 12
                                })
                            });

                            if (!response.ok) {
                                const errText = await response.text().catch(() => 'Unknown error');
                                throw new Error(`API ${response.status} for "${spotName}": ${errText}`);
                            }

                            const data = await response.json();
                            const nameFormatted = (data.attraction || spotName)
                                .toLowerCase()
                                .replace(/\b\w/g, s => s.toUpperCase());

                            let finalForecasts = data.forecasts;
                            let finalMape = parseFloat(data.mape);
                            let finalInterpretation = data.mape_interpretation;
                            let isFallback = false;

                            // Selective Fallback Mechanism:
                            // When the model exceeds a 100% MAPE threshold, 
                            // the system automatically switches to Seasonal Mean Forecasting.
                            if (finalMape > 100) {
                                console.warn(`MAPE for ${spotName} is ${finalMape}%. Switching to Seasonal Mean Forecasting fallback.`);
                                finalForecasts = this.calculateSeasonalMean(data.historical_monthly || data.historical_test || []);
                                finalInterpretation = "Seasonal Mean (Selective Fallback)";
                                isFallback = true;
                            }

                            return {
                                name: data.attraction,
                                name_formatted: nameFormatted,
                                mape: finalMape,
                                mape_interpretation: finalInterpretation,
                                isFallback: isFallback,
                                sarima_order: data.sarima_order,
                                seasonal_analysis: data.seasonal_analysis,
                                peak_seasons: data.peak_seasons,
                                raw_forecasts: finalForecasts,
                                historical_monthly: data.historical_monthly || [],
                                historical_test: data.historical_test || [],
                                test_predicted: data.test_predicted || [],
                                historical_yearly: data.historical_yearly || []
                            };
                        });

                        const results = await Promise.allSettled(requests);
                        results.forEach((result, idx) => {
                            if (result.status === 'fulfilled') {
                                this.forecasts.push(result.value);
                            } else {
                                console.error(`Failed forecast for "${spotNames[idx]}":`, result.reason);
                            }
                        });

                        console.log(`Successfully loaded ${this.forecasts.length} / ${spotNames.length} forecasts:`,
                            this.forecasts.map(f => f.name));

                        // Populate month dropdown options based on the first successful forecast timeline
                        if (this.forecasts.length > 0) {
                            this.availableMonths = this.forecasts[0].raw_forecasts.map(f => ({ value: f.month, label: this.formatMonth(f.month) }));
                            this.$nextTick(() => this.buildForecastChart());
                        }

                        if (this.forecasts.length === 0) this.error = "Prediction Server Unreachable";
                    } catch (e) {
                        console.error('Forecast init error:', e);
                        this.error = "Connection Refused.";
                    }
                    finally { this.loading = false; }
                }
            }
        }
    </script>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- Alpine: Area Breakdown Component --}}
    {{-- ══════════════════════════════════════════════ --}}
    <script>
        function areaBreakdown() {
            const now = new Date();
            return {
                // ── State ──
                currentYear: now.getFullYear(),
                selectedYear: now.getFullYear(),
                selectedMonth: now.getMonth() + 1,  // 1-indexed
                selectedDay: null,
                availableDays: [],
                tableRows: [],
                lastSync: null,
                daysLoading: false,
                tableLoading: false,

                monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

                // ── Lifecycle ──
                async init() {
                    await this.fetchDays();
                },

                // ── Actions ──
                async selectMonth(m) {
                    this.selectedMonth = m;
                    this.selectedDay = null;
                    await this.fetchDays();
                },

                async selectDay(d) {
                    this.selectedDay = d;
                    await this.fetchTable();
                },

                // ── API calls ──
                async fetchDays() {
                    this.daysLoading = true;
                    this.availableDays = [];
                    this.tableRows = [];
                    this.selectedDay = null;

                    try {
                        const url = `/api/statistics/month-days?year=${this.selectedYear}&month=${this.selectedMonth}`;
                        const res = await fetch(url);
                        if (!res.ok) throw new Error('Request failed');
                        const data = await res.json();

                        this.availableDays = data.days || [];
                        if (data.last_sync) this.lastSync = data.last_sync;
                    } catch (e) {
                        console.error('Area breakdown – fetchDays error:', e);
                    } finally {
                        this.daysLoading = false;
                    }

                    // After loading days always fetch table for the whole month
                    await this.fetchTable();
                },

                async fetchTable() {
                    this.tableLoading = true;

                    try {
                        let url = `/api/statistics/area-breakdown?year=${this.selectedYear}&month=${this.selectedMonth}`;
                        if (this.selectedDay) url += `&day=${this.selectedDay}`;

                        const res = await fetch(url);
                        if (!res.ok) throw new Error('Request failed');
                        const data = await res.json();

                        this.tableRows = data.rows || [];
                        if (data.last_sync) this.lastSync = data.last_sync;
                    } catch (e) {
                        console.error('Area breakdown – fetchTable error:', e);
                        this.tableRows = [];
                    } finally {
                        this.tableLoading = false;
                    }
                },
            };
        }
    </script>

    {{-- ══════════════════════════ --}}
    {{-- Chart.js Initialization --}}
    {{-- ══════════════════════════ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const fontFamily = 'Inter';
            const gridColor = 'rgba(226,232,240,0.6)';
            const tickColor = '#64748b';

            // ── 1. Monthly Trend (Line Chart) ──
            new Chart(document.getElementById('trendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Visitors',
                        data: @json($trendData),
                        borderColor: '#008080',
                        backgroundColor: function (ctx) {
                            const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(0,128,128,0.25)');
                            gradient.addColorStop(1, 'rgba(0,128,128,0.01)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#008080',
                        pointBorderWidth: 2,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.9)',
                            titleFont: { family: fontFamily, size: 12 },
                            bodyFont: { family: fontFamily, size: 14, weight: 'bold' },
                            padding: 12, cornerRadius: 8, displayColors: false,
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { font: { family: fontFamily }, color: tickColor } },
                        x: { grid: { display: false }, ticks: { font: { family: fontFamily, size: 11 }, color: tickColor, maxRotation: 45, minRotation: 25 } },
                    }
                }
            });

            // ── 2. Visitors per Spot (Bar Chart) ──
            const barColors = [
                'rgba(0, 128, 128, 0.8)', 'rgba(0, 119, 190, 0.8)', 'rgba(0, 162, 237, 0.8)',
                'rgba(60, 99, 130, 0.8)', 'rgba(10, 61, 98, 0.8)', 'rgba(130, 204, 221, 0.8)',
                'rgba(30, 55, 153, 0.8)', 'rgba(0, 194, 224, 0.8)', 'rgba(0, 210, 211, 0.8)',
            ];
            const spotData = {!! $spotData !!};
            new Chart(document.getElementById('spotsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! $spotLabels !!},
                    datasets: [{
                        label: 'Total Visitors',
                        data: spotData,
                        backgroundColor: barColors.slice(0, spotData.length),
                        borderColor: barColors.map(c => c.replace('0.8', '1')).slice(0, spotData.length),
                        borderWidth: 1,
                        borderRadius: 8,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', titleFont: { family: fontFamily, size: 12 }, bodyFont: { family: fontFamily, size: 14, weight: 'bold' }, padding: 12, cornerRadius: 8, displayColors: false },
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { font: { family: fontFamily }, color: tickColor } },
                        x: { grid: { display: false }, ticks: { font: { family: fontFamily, size: 11 }, color: '#475569', maxRotation: 45, minRotation: 45 } },
                    },
                    animation: { y: { duration: 1000, easing: 'easeOutQuart' } }
                }
            });

            // ── 3. Visit Reason (Doughnut) ──
            const reasonLabels = @json($reasonStats->keys());
            const reasonData = @json($reasonStats->values());
            const doughnutColors = ['rgba(0, 128, 128, 0.85)', 'rgba(0, 119, 190, 0.85)', 'rgba(0, 162, 237, 0.85)', 'rgba(130, 204, 221, 0.85)', 'rgba(30, 55, 153, 0.85)'];

            new Chart(document.getElementById('reasonChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: reasonLabels,
                    datasets: [{
                        data: reasonData,
                        backgroundColor: doughnutColors.slice(0, reasonData.length),
                        borderWidth: 0,
                        hoverOffset: 12,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', bodyFont: { family: fontFamily, size: 13 }, padding: 10, cornerRadius: 8 },
                    }
                }
            });

            // ── 4. Origin (Pie) ──
            const originLabels = @json($originStats->keys());
            const originData = @json($originStats->values());
            const pieColors = [
                'rgba(0, 128, 128, 0.85)', 'rgba(0, 119, 190, 0.85)', 'rgba(0, 162, 237, 0.85)',
                'rgba(130, 204, 221, 0.85)', 'rgba(30, 55, 153, 0.85)', 'rgba(10, 61, 98, 0.85)'
            ];

            // Ensure we have enough colors if there are many origins
            const dynamicColors = originLabels.map((_, i) => pieColors[i % pieColors.length]);

            new Chart(document.getElementById('originChart').getContext('2d'), {
                type: 'pie', // Using a pie chart for origins as requested
                data: {
                    labels: originLabels,
                    datasets: [{
                        data: originData,
                        backgroundColor: dynamicColors,
                        borderWidth: 0,
                        hoverOffset: 12,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', bodyFont: { family: fontFamily, size: 13 }, padding: 10, cornerRadius: 8 },
                    }
                }
            });
        });
    </script>
</body>

</html>