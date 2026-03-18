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
            <span class="text-xl font-heading font-bold text-slate-800 tracking-tight">
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

            {{-- Common Links --}}
            <!-- <a href="#" class="flex items-center px-4 py-3 text-slate-600 transition-colors transform rounded-xl hover:bg-slate-100 hover:text-slate-900">
                <i class="fa-solid fa-gear w-6"></i>
                <span class="mx-2 font-medium">Settings</span>
            </a> -->
        </nav>

        <div class="mt-6 border-t border-slate-100 pt-6">
            <div class="p-4 bg-slate-50 rounded-2xl mb-4 border border-slate-100 hover:border-slate-200 transition-all duration-300">
                <div class="flex items-center gap-3 pl-0.5">
                    <div class="w-10 h-10 rounded-full bg-[#008080]/10 flex items-center justify-center text-[#008080] shrink-0 border border-[#008080]/20 shadow-sm">
                        <i class="fa-solid fa-circle-user text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate tracking-tight leading-tight">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-[10px] font-black text-[#008080] uppercase tracking-widest mt-1 opacity-80 leading-tight">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 px-4 py-3 text-slate-500 font-bold text-sm uppercase tracking-wider border border-slate-200 rounded-xl transition-all duration-300 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 hover:shadow-sm group active:scale-[0.98]">
                    <i class="fa-solid fa-power-off group-hover:rotate-90 transition-transform duration-500"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>

