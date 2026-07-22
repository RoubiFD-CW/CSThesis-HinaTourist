<nav x-data="{ open: false, scroll: false }" @scroll.window="scroll = (window.pageYOffset > 20) ? true : false"
    :class="scroll ? 'bg-white/80 backdrop-blur-md shadow-sm' : 'bg-transparent'"
    class="fixed w-full z-50 transition-all duration-300 top-0 left-0 border-b border-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-1 group">
                    <img src="{{ asset('ht-final-logo.svg') }}"
                        class="h-48 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                        alt="HinaTourist">
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-8 items-center">
                <a href="{{ request()->routeIs('destinations') ? url('/home') : '/#hero' }}"
                    :class="scroll || {{ request()->routeIs('destinations') ? 'true' : 'false' }} ? 'text-cyan-700 hover:text-cyan-900' : 'text-white hover:text-white/80'"
                    class="font-medium transition-colors text-sm uppercase tracking-wide">Home</a>
                <a href="{{ request()->routeIs('destinations') ? url('/home') : '#destinations' }}"
                    :class="scroll || {{ request()->routeIs('destinations') ? 'true' : 'false' }} ? 'text-cyan-700 hover:text-cyan-900' : 'text-white hover:text-white/80'"
                    class="font-medium transition-colors text-sm uppercase tracking-wide">Destinations</a>

                <!-- <a href="#about"
                    :class="scroll ? 'text-cyan-700 hover:text-cyan-900' : 'text-white hover:text-white/80'"
                    class="font-medium transition-colors text-sm uppercase tracking-wide">About</a> -->

                @auth
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('user.dashboard') }}"
                        class="px-5 py-2.5 rounded-full bg-[#008080] shadow-md shadow-[#008080]/20 text-white font-medium hover:bg-[#006666] transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm">
                        Dashboard
                    </a>
                @else
                    <div class="flex items-center gap-4 border-l pl-6"
                        :class="scroll ? 'border-slate-200' : 'border-white/30'">
                        <a href="{{ route('login') }}"
                            class="px-5 py-2.5 rounded-full bg-slate-900 text-white font-medium hover:bg-slate-800 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm">
                            Log in
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="-mr-2 flex items-center md:hidden">
                <button @click="open = !open" type="button"
                    :class="scroll || {{ request()->routeIs('destinations') ? 'true' : 'false' }} ? 'hover:scale-110' : 'text-white hover:text-white/80'"
                    class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition-transform">
                    <i class="fa-solid fa-bars text-xl text-transparent bg-clip-text bg-[linear-gradient(to_bottom,#008080,#1A4B9F)]"
                        x-show="!open"></i>
                    <i class="fa-solid fa-xmark text-xl text-transparent bg-clip-text bg-[linear-gradient(to_bottom,#008080,#1A4B9F)]"
                        x-show="open" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition.opacity
        class="md:hidden bg-white/95 backdrop-blur-xl border-b border-slate-100 absolute w-full left-0 top-20 shadow-xl"
        x-cloak>
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ request()->routeIs('destinations') ? url('/home') : url('/') }}"
                class="block px-4 py-3 rounded-lg text-base font-medium text-slate-700 hover:text-[#008080] hover:bg-[#008080]/10 transition-colors">Home</a>
            <a href="{{ request()->routeIs('destinations') ? url('/home') : '#destinations' }}"
                class="block px-4 py-3 rounded-lg text-base font-medium text-slate-700 hover:text-[#008080] hover:bg-[#008080]/10 transition-colors">Destinations</a>


            <div class="border-t border-slate-100 my-2 pt-2">
                @auth
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('user.dashboard') }}"
                        class="block w-full text-center px-4 py-3 rounded-lg bg-[#008080] shadow-md shadow-[#008080]/20 text-white font-medium shadow-md">Dashboard</a>
                @else
                    <div class="px-2">
                        <a href="{{ route('login') }}"
                            class="flex justify-center items-center px-4 py-3 rounded-lg bg-slate-900 text-white font-medium hover:bg-slate-800 shadow-md">Log
                            in</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>