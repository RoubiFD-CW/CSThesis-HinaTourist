<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PWA App') }} | User Management</title>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        @media (min-width: 1024px) {
            .desktop-modal-offset {
                left: 16rem !important;
            }
        }
    </style>
</head>

<body x-data="{ 
        sidebarOpen: false, 
        deleteModal: false, 
        deleteAction: '', 
        deleteName: '',
        addModal: {{ $errors->any() ? 'true' : 'false' }},
        successModal: {{ session('account_created') ? 'true' : 'false' }},
        resendModal: false,
        resendAction: '',
        editModal: false,
        editAction: '',
        editData: {
            name: '',
            email: '',
            dedicated_area: ''
        }
    }"
    class="antialiased bg-slate-50 text-slate-800 selection:bg-[#008080] selection:text-white h-screen overflow-y-auto flex flex-col lg:flex-row">

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

        <div class="w-full px-0 sm:px-2 pt-0 pb-4">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mb-1">User Management
                    </h1>
                    <p class="text-slate-500 text-sm">Create and manage site attendant accounts.</p>
                </div>
            </div>



            {{-- User List --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 sm:p-6 gap-4 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-users text-slate-400"></i> Existing Accounts
                    </h2>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full sm:w-auto">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search users..."
                                    class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all text-sm">
                                <i
                                    class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            </div>
                        </form>

                        <button @click="addModal = true"
                            class="w-full sm:w-auto px-5 py-2.5 bg-[#008080] shadow-md shadow-[#008080]/20 hover:bg-[#006666] text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-[#008080]/20 active:scale-[0.98] flex items-center justify-center gap-2 whitespace-nowrap">
                            <i class="fa-solid fa-user-plus"></i> Add Attendant
                        </button>
                    </div>
                </div>

                @if($attendants->isEmpty())
                    <div class="text-center py-12 px-6">
                        <div
                            class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <i class="fa-solid fa-users-slash text-xl"></i>
                        </div>
                        <p class="text-slate-500 text-sm">
                            @if(request('search'))
                                No users found matching "{{ request('search') }}".
                            @else
                                No site attendants created yet.
                            @endif
                        </p>
                        @if(request('search'))
                            <a href="{{ route('admin.users.index') }}"
                                class="text-[#008080] font-medium text-xs hover:underline mt-2 inline-block">Clear
                                Search</a>
                        @endif
                    </div>
                @else
                    {{-- Mobile Card View --}}
                    <div class="lg:hidden divide-y divide-slate-100">
                        @foreach($attendants as $attendant)
                            <div class="p-4 hover:bg-slate-50/50 transition-colors">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-900 truncate">{{ $attendant->name }}</p>
                                            <p class="text-sm text-slate-500 truncate mt-0.5">{{ $attendant->email }}</p>
                                            <span
                                                class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                                <i class="fa-solid fa-map-pin mr-1 text-[10px]"></i>
                                                {{ $attendant->dedicated_area }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-slate-100/60">
                                        <div>
                                            @if($attendant->hasVerifiedEmail())
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">
                                                    <i class="fa-solid fa-circle-check"></i> Verified
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">
                                                    <i class="fa-solid fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if(!$attendant->hasVerifiedEmail())
                                                <form action="{{ route('admin.attendants.resend', $attendant->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" title="Resend Verification Email"
                                                        class="text-amber-500 hover:text-amber-600 p-2 rounded-lg hover:bg-amber-500/15 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                                        <i class="fa-solid fa-envelope-open-text drop-shadow-sm"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" title="Edit Account"
                                                @click="editModal = true; editAction = '{{ route('admin.attendants.update', $attendant->id) }}'; editData = { name: '{{ addslashes($attendant->name) }}', email: '{{ $attendant->email }}', dedicated_area: '{{ $attendant->dedicated_area }}' }"
                                                class="text-indigo-400 hover:text-indigo-600 p-2 rounded-lg hover:bg-indigo-500/15 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-user-pen drop-shadow-sm"></i>
                                            </button>
                                            <button type="button" title="Delete Account"
                                                @click="deleteModal = true; deleteAction = '{{ route('admin.attendants.destroy', $attendant->id) }}'; deleteName = '{{ addslashes($attendant->name) }}'"
                                                class="text-rose-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-500/15 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-trash-can drop-shadow-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table View --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Name</th>
                                    <th class="px-6 py-3 font-medium">Email</th>
                                    <th class="px-6 py-3 font-medium">Dedicated Area</th>
                                    <th class="px-6 py-3 font-medium">Status</th>
                                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($attendants as $attendant)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-slate-900">{{ $attendant->name }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $attendant->email }}</td>
                                        <td class="px-6 py-4 text-slate-500">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100/50">
                                                {{ $attendant->dedicated_area }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($attendant->hasVerifiedEmail())
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                                                    <i class="fa-solid fa-circle-check"></i> Verified
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-100">
                                                    <i class="fa-solid fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @if(!$attendant->hasVerifiedEmail())
                                                    <form action="{{ route('admin.attendants.resend', $attendant->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" title="Resend Verification Email"
                                                            class="text-amber-500 hover:text-amber-600 p-2 rounded-lg hover:bg-amber-500/15 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                                            <i class="fa-solid fa-envelope-open-text drop-shadow-sm"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button" title="Edit Account"
                                                    @click="editModal = true; editAction = '{{ route('admin.attendants.update', $attendant->id) }}'; editData = { name: '{{ addslashes($attendant->name) }}', email: '{{ $attendant->email }}', dedicated_area: '{{ $attendant->dedicated_area }}' }"
                                                    class="text-indigo-400 hover:text-indigo-600 p-2 rounded-lg hover:bg-indigo-500/15 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                                    <i class="fa-solid fa-user-pen drop-shadow-sm"></i>
                                                </button>
                                                <button type="button" title="Delete Account"
                                                    @click="deleteModal = true; deleteAction = '{{ route('admin.attendants.destroy', $attendant->id) }}'; deleteName = '{{ addslashes($attendant->name) }}'"
                                                    class="text-rose-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-500/15 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                                    <i class="fa-solid fa-trash-can drop-shadow-sm"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="p-4 sm:p-6 border-t border-slate-100">
                        {{ $attendants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Add Attendant Modal --}}
    <div x-show="addModal"
        class="fixed inset-0 desktop-modal-offset z-[100] flex items-center justify-center p-4 sm:p-0" x-cloak>
        <!-- Backdrop -->
        <div x-show="addModal" x-transition.opacity
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="addModal = false">
        </div>
        <!-- Modal Panel -->
        <div x-show="addModal" x-transition.opacity.scale.95
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-visible transform transition-all flex flex-col max-h-[90vh]">

            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-[#008080]"></i> Add Attendant
                </h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-5 sm:p-6 overflow-visible flex-1">
                @if($errors->any())
                    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.attendants.store') }}" method="POST" id="addAttendantForm" @submit="
                        const emailInput = $event.target.querySelector('input[name=email]');
                        if(!emailInput.value.endsWith('@gmail.com')){
                            $event.preventDefault();
                            alert('Only @gmail.com addresses are allowed for authenticity verification.');
                            emailInput.focus();
                        }
                    ">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="example@gmail.com" pattern=".*@gmail\.com$"
                                title="Please enter a valid @gmail.com address"
                                class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                            <i class="fa-brands fa-google absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Must be a valid @gmail.com account for verification.</p>
                    </div>

                    <div class="mb-4" x-data="{
                        open: false,
                        search: '{{ old('dedicated_area') }}',
                        selected: '{{ old('dedicated_area') }}',
                        options: [
                            { value: 'Enchanted River', label: 'Enchanted River' },
                            { value: 'Hinatuan Adventure Park', label: 'Hinatuan Adventure Park' },
                            { value: 'Lodestone Shores Resort', label: 'Lodestone Shores Resort' },
                            { value: 'Baculin Amazing Sand Bar', label: 'Baculin Amazing Sand Bar' },
                            { value: 'Harip Oceanside Beach', label: 'Harip Oceanside Beach' },
                            { value: 'Rock Island Resort', label: 'Rock Island Resort' },
                            { value: 'Mamaon Beach Resort', label: 'Mamaon Beach Resort' },
                            { value: 'Amparitas Integrated Nature Farm', label: 'Amparitas Integrated Nature Farm' },
                            { value: 'Sibadan Fish Cage and Resort', label: 'Sibadan Fish Cage and Resort' },
                            { value: 'Landong Bay', label: 'Landong Bay' },
                            { value: 'Davince Hidden Paradise', label: 'Davince Hidden Paradise' },
                            { value: 'Tarusan Cold Spring', label: 'Tarusan Cold Spring' },
                            { value: 'Llamas Beach Resort', label: 'Llamas Beach Resort' },
                            { value: 'Puro Brigida’s Beach', label: 'Puro Brigida’s Beach' },
                            { value: 'Bunsadan Falls', label: 'Bunsadan Falls' }
                        ],
                        get filteredOptions() {
                            if (this.search === '') return this.options;
                            return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        selectOption(option) {
                            this.selected = option.value;
                            this.search = option.label;
                            this.open = false;
                        },
                        revertSearch() {
                            const found = this.options.find(o => o.value === this.selected);
                            this.search = found ? found.label : '';
                        }
                    }">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Dedicated Area</label>
                        <input type="hidden" name="dedicated_area" :value="selected" required>

                        <div class="relative">
                            <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                @click.away="open = false; revertSearch();" @input="open = true"
                                @keydown.escape="open = false; revertSearch();"
                                @keydown.enter.prevent="if(filteredOptions.length > 0) selectOption(filteredOptions[0])"
                                placeholder="Select or search an area"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all bg-white cursor-text"
                                autocomplete="off">
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition.opacity.duration.200ms
                                class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto"
                                style="display: none;">
                                <ul class="py-1">
                                    <template x-for="option in filteredOptions" :key="option.value">
                                        <li class="px-4 py-2.5 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                            :class="selected === option.value ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
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

                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <button type="button" @click="addModal = false"
                            class="w-full sm:w-1/3 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition-colors focus:ring-2 focus:ring-slate-200 focus:outline-none">
                            Cancel
                        </button>
                        <button type="submit"
                            class="w-full sm:w-2/3 px-4 py-3 bg-[#008080] shadow-md shadow-[#008080]/20 hover:bg-[#006666] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#008080]/20 active:scale-[0.98]">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Attendant Modal --}}
    <div x-show="editModal"
        class="fixed inset-0 desktop-modal-offset z-[100] flex items-center justify-center p-4 sm:p-0" x-cloak>
        <!-- Backdrop -->
        <div x-show="editModal" x-transition.opacity
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="editModal = false">
        </div>
        <!-- Modal Panel -->
        <div x-show="editModal" x-transition.opacity.scale.95
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-[#008080]"></i> Edit Attendant
                    </h2>
                    <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Registered Name</label>
                            <input type="text" name="name" x-model="editData.name" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all"
                                placeholder="Full name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Gmail Address</label>
                            <input type="email" name="email" x-model="editData.email" required pattern=".*@gmail\.com$"
                                title="Must be a valid @gmail.com address"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all"
                                placeholder="example@gmail.com">
                            <p class="text-xs text-amber-600 mt-1.5 flex items-start gap-1.5">
                                <i class="fa-solid fa-circle-info mt-0.5"></i>
                                <span>Changing the email will require the account to be re-verified.</span>
                            </p>
                        </div>

                        <div x-data="{
                            open: false,
                            search: '',
                            selected: '',
                            options: [
                                { value: 'Enchanted River', label: 'Enchanted River' },
                                { value: 'Hinatuan Adventure Park', label: 'Hinatuan Adventure Park' },
                                { value: 'Lodestone Shores Resort', label: 'Lodestone Shores Resort' },
                                { value: 'Baculin Amazing Sand Bar', label: 'Baculin Amazing Sand Bar' },
                                { value: 'Harip Oceanside Beach', label: 'Harip Oceanside Beach' },
                                { value: 'Rock Island Resort', label: 'Rock Island Resort' },
                                { value: 'Mamaon Beach Resort', label: 'Mamaon Beach Resort' },
                                { value: 'Amparitas Integrated Nature Farm', label: 'Amparitas Integrated Nature Farm' },
                                { value: 'Sibadan Fish Cage and Resort', label: 'Sibadan Fish Cage and Resort' },
                                { value: 'Landong Bay', label: 'Landong Bay' },
                                { value: 'Davince Hidden Paradise', label: 'Davince Hidden Paradise' },
                                { value: 'Tarusan Cold Spring', label: 'Tarusan Cold Spring' },
                                { value: 'Llamas Beach Resort', label: 'Llamas Beach Resort' },
                                { value: 'Puro Brigida’s Beach', label: 'Puro Brigida’s Beach' },
                                { value: 'Bunsadan Falls', label: 'Bunsadan Falls' }
                            ],
                            get filteredOptions() {
                                if (this.search === '') return this.options;
                                return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            selectOption(option) {
                                this.selected = option.value;
                                this.search = option.label;
                                editData.dedicated_area = option.value;
                                this.open = false;
                            },
                            revertSearch() {
                                const found = this.options.find(o => o.value === this.selected);
                                this.search = found ? found.label : '';
                            }
                        }"
                            x-init="$watch('editModal', value => { if(value) { selected = editData.dedicated_area; revertSearch(); } })">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Dedicated Area</label>
                            <input type="hidden" name="dedicated_area" :value="selected" required>

                            <div class="relative">
                                <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                    @click.away="open = false; revertSearch();" @input="open = true"
                                    @keydown.escape="open = false; revertSearch();"
                                    @keydown.enter.prevent="if(filteredOptions.length > 0) selectOption(filteredOptions[0])"
                                    placeholder="Select or search an area"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all bg-white cursor-text"
                                    autocomplete="off">
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform"
                                    :class="open ? 'rotate-180' : ''"></i>

                                <!-- Dropdown -->
                                <div x-show="open" x-transition.opacity.duration.200ms
                                    class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto"
                                    style="display: none;">
                                    <ul class="py-1">
                                        <template x-for="option in filteredOptions" :key="option.value">
                                            <li class="px-4 py-2.5 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                                :class="selected === option.value ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
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

                    <div class="flex gap-3 mt-8">
                        <button type="button" @click="editModal = false"
                            class="w-full sm:w-1/2 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-colors active:scale-[0.98]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="w-full sm:w-1/2 px-4 py-3 rounded-xl bg-[#008080] shadow-md shadow-[#008080]/20 text-white font-bold hover:bg-[#006666] shadow-lg shadow-[#008080]/20 transition-all active:scale-[0.98]">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Success Confirmation Modal --}}
    <div x-show="successModal"
        class="fixed inset-0 desktop-modal-offset z-[100] flex items-center justify-center p-4 sm:p-0" x-cloak>
        <div x-show="successModal" x-transition.opacity
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="successModal = false">
        </div>
        <div x-show="successModal" x-transition.opacity.scale.95
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all text-center p-6 sm:p-8">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-6">
                <i class="fa-solid fa-envelope-circle-check text-3xl text-emerald-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Account Successfully Created!</h3>
            <p class="text-slate-500 text-sm sm:text-base mb-8 leading-relaxed">
                A verification link has been sent to the Gmail provided. The account will remain <span
                    class="font-bold text-amber-600">Pending</span> and login will be restricted until verified.
            </p>
            <button type="button" @click="successModal = false; window.location.href='{{ route('admin.users.index') }}'"
                class="w-full px-4 py-3 rounded-xl bg-[#008080] shadow-md shadow-[#008080]/20 text-white font-bold hover:bg-[#006666] shadow-lg shadow-[#008080]/20 transition-all active:scale-[0.98]">
                Got it
            </button>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal"
        class="fixed inset-0 desktop-modal-offset z-[100] flex items-center justify-center p-4 sm:p-0" x-cloak>
        <div x-show="deleteModal" x-transition.opacity
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="deleteModal = false">
        </div>
        <div x-show="deleteModal" x-transition.opacity.scale.95
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all p-6 sm:p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 mb-6">
                <i class="fa-solid fa-trash-can text-2xl text-rose-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Delete Attendant Account</h3>
            <p class="text-slate-500 text-sm sm:text-base mb-8 leading-relaxed">
                Are you sure you want to permanently delete <span class="font-bold text-slate-800"
                    x-text="deleteName"></span>? This action cannot be undone.
            </p>
            <form :action="deleteAction" method="POST" class="mt-2">
                @csrf
                @method('DELETE')
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="deleteModal = false"
                        class="w-full sm:w-1/2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 hover:text-slate-900 transition-colors focus:ring-2 focus:ring-slate-200 focus:outline-none">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-full sm:w-1/2 px-4 py-2.5 rounded-xl bg-rose-600 text-white font-medium hover:bg-rose-700 shadow-lg shadow-rose-600/20 transition-all focus:ring-2 focus:ring-rose-500 focus:outline-none active:scale-[0.98]">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>

</html>