<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'HinaTourist') }} | My Profile</title>
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

        /* Unified focus style for profile inputs */
        .profile-input:focus {
            border-color: #008080 !important;
            box-shadow: 0 0 0 3px rgba(22, 125, 119, 0.15) !important;
            outline: none !important;
        }
    </style>
</head>

<body x-data="{ sidebarOpen: false }"
    class="antialiased bg-slate-50 text-slate-800 selection:bg-brand selection:text-white h-screen overflow-y-auto flex flex-col lg:flex-row">

    {{-- Navigation --}}
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
    <main class="flex-1 flex flex-col relative overflow-y-auto w-full px-4 pt-6 pb-6 sm:px-8 sm:pt-8 sm:pb-10">
        <!-- Background Decor -->
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-[15%] right-[10%] w-[35%] h-[35%] rounded-full bg-rose-100/40 blur-[100px]"></div>
            <div class="absolute bottom-[10%] left-[5%] w-[30%] h-[30%] rounded-full bg-amber-100/30 blur-[80px]"></div>
        </div>

        <div class="w-full max-w-3xl mx-auto px-0 sm:px-2 pt-0 pb-4">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mb-1">My Profile</h1>
                <p class="text-slate-500 text-sm">Manage your account settings and credentials.</p>
            </div>





            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 sm:p-8">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf

                        <!-- Personal Info Section -->
                        <div class="mb-8">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-slate-100 pb-2.5 gap-4">
                                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                    <i class="fa-solid fa-user text-[#008080] text-sm"></i> Account Details
                                </h2>
                                <a href="{{ Auth::user()->is_admin ? route('admin.dashboard') : route('user.dashboard') }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-[#008080] transition-colors group shrink-0 bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                    <i
                                        class="fa-solid fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-0.5"></i>
                                    Back to Dashboard
                                </a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1.5">Full Name</label>
                                    <div class="relative">
                                        <i
                                            class="fa-regular fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        <input type="text" value="{{ $user->name }}" disabled
                                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">Registered name cannot be changed.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1.5">Dedicated
                                        Area</label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-map-pin absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        <input type="text"
                                            value="{{ $user->is_admin ? 'Administrator' : $user->dedicated_area }}"
                                            disabled
                                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed font-medium">
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">Assigned area cannot be changed.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Credentials Section -->
                        <div class="mb-8">
                            <h2
                                class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2 border-b border-slate-100 pb-2.5">
                                <i class="fa-solid fa-shield-halved text-[#008080] text-sm"></i> Login Credentials
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1.5">Email Address</label>
                                    <div class="relative">
                                        <i
                                            class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                            required @if(!$user->is_admin) pattern=".*@gmail\.com$"
                                            title="Must be a valid @gmail.com address" @endif
                                            class="profile-input w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm transition-all">
                                    </div>
                                    @if(!$user->is_admin)
                                        <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            Changing email requires re-verification.
                                        </p>
                                    @endif
                                </div>

                                <div x-data="{ show: false }">
                                    <label class="block text-sm font-medium text-slate-600 mb-1.5">New Password</label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        <input :type="show ? 'text' : 'password'" name="password"
                                            placeholder="Leave blank to keep current"
                                            class="profile-input w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm transition-all">
                                        <button type="button" @click="show = !show"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                                            <i class="fa-regular text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">Min 8 characters.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 bg-[#008080] text-white text-sm font-semibold rounded-xl shadow-md shadow-[#008080]/15 hover:bg-[#006666] hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-2">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

</body>

</html>