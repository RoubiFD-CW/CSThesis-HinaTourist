<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
    class="fixed inset-0 bg-slate-900/80 z-30 lg:hidden"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full shadow-none'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:shadow-none flex flex-col h-screen overflow-y-auto shrink-0">

    <div class="flex items-center justify-between px-6 mb-10 mt-8">
        <div class="flex items-center gap-3">
            <div class="shrink-0">
                <img src="{{ asset('hinatourist-logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
            </div>
            <span
                class="text-xl font-heading font-black tracking-tight text-transparent bg-clip-text bg-[linear-gradient(to_bottom,#008080,#1A4B9F)]">
                {{ config('app.name') }}
            </span>
        </div>
        <!-- Close Button (Mobile Only) -->
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <div class="flex flex-col justify-between flex-1 px-4 pb-8">
        <nav class="space-y-3">
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Main Menu</p>

            {{-- Dashboard Link (Dynamic based on role) --}}
            <a href="{{ Auth::user()->is_admin ? route('admin.dashboard') : route('user.dashboard') }}"
                class="flex items-center px-4 py-3 text-slate-600 transition-colors transform rounded-xl hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') ? 'bg-[#008080]/10 text-[#008080] font-medium' : '' }}">
                <i class="fa-solid fa-chart-pie w-6"></i>
                <span class="mx-2 font-medium">Dashboard</span>
            </a>

            {{-- User Management (Admin Only) --}}
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.statistics') }}"
                    class="flex items-center px-4 py-3 text-slate-600 transition-colors transform rounded-xl hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('admin.statistics') ? 'bg-[#008080]/10 text-[#008080] font-medium' : '' }}">
                    <i class="fa-solid fa-chart-column w-6"></i>
                    <span class="mx-2 font-medium">Statistics</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-3 text-slate-600 transition-colors transform rounded-xl hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('admin.users.index') ? 'bg-[#008080]/10 text-[#008080] font-medium' : '' }}">
                    <i class="fa-solid fa-users w-6"></i>
                    <span class="mx-2 font-medium">User Management</span>
                </a>
            @endif

            {{-- Visitor Logbook (Available for all) --}}
            <a href="{{ route('logbook.index') }}"
                class="flex items-center px-4 py-3 text-slate-600 transition-colors transform rounded-xl hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('logbook.index') ? 'bg-[#008080]/10 text-[#008080] font-medium' : '' }}">
                <i class="fa-solid fa-address-book w-6"></i>
                <span class="mx-2 font-medium">Logbook</span>
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-100">
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Profile</p>
            <div x-data="{ hover: false }">
                <a href="{{ route('profile.show') }}" @mouseenter="hover = true" @mouseleave="hover = false"
                    class="block p-4 rounded-2xl mb-3 border transition-all duration-300 focus:outline-none {{ request()->routeIs('profile.show') ? 'bg-[#008080]/10 border-[#008080]/20' : 'bg-slate-50 border-slate-100' }}"
                    :class="hover ? 'bg-[#008080]/10 border-[#008080]/20' : '{{ request()->routeIs('profile.show') ? 'bg-[#008080]/10 border-[#008080]/20' : 'bg-slate-50 border-slate-100' }}'">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-[#008080]/20 bg-[#008080]/10 text-[#008080]">
                                <i class="fa-solid fa-circle-user text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate leading-tight text-[#008080]">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-[10px] font-medium text-slate-500 truncate mt-0.5">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] transition-colors text-[#008080]"></i>
                    </div>
                </a>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 px-4 py-3 text-slate-500 font-bold text-xs uppercase tracking-wider border border-slate-200 rounded-xl transition-all duration-300 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 group active:scale-[0.98]">
                    <i
                        class="fa-solid fa-right-from-bracket group-hover:translate-x-1 transition-transform duration-300"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- Global Toast System --}}
@include('dashboard.partials.toast')