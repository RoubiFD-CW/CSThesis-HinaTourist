<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PWA App') }} | Admin Dashboard</title>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* From Uiverse.io by Yaya12085 */
        .card-modal {
            overflow: hidden;
            position: relative;
            background-color: #ffffff;
            text-align: left;
            border-radius: 0.5rem;
            max-width: 290px;
            box-shadow:
                0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .header-modal {
            padding: 1.25rem 1rem 1rem 1rem;
            background-color: #ffffff;
        }

        .image-modal {
            display: flex;
            margin-left: auto;
            margin-right: auto;
            background-color: #fee2e2;
            flex-shrink: 0;
            justify-content: center;
            align-items: center;
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
        }

        .image-modal svg {
            color: #dc2626;
            width: 1.5rem;
            height: 1.5rem;
        }

        .content-modal {
            margin-top: 0.75rem;
            text-align: center;
        }

        .title-modal {
            color: #111827;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.5rem;
        }

        .message-modal {
            margin-top: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .actions-modal {
            margin: 0.75rem 1rem;
            background-color: #f9fafb;
        }

        .desactivate-modal {
            display: inline-flex;
            padding: 0.5rem 1rem;
            background-color: #dc2626;
            color: #ffffff;
            font-size: 1rem;
            line-height: 1.5rem;
            font-weight: 500;
            justify-content: center;
            width: 100%;
            border-radius: 0.375rem;
            border-width: 1px;
            border-color: transparent;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .cancel-modal {
            display: inline-flex;
            margin-top: 0.75rem;
            padding: 0.5rem 1rem;
            background-color: #ffffff;
            color: #374151;
            font-size: 1rem;
            line-height: 1.5rem;
            font-weight: 500;
            justify-content: center;
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }
    </style>
</head>

<body x-data="{ sidebarOpen: false, deleteModal: false, deleteAction: '', deleteName: '' }"
    class="antialiased bg-slate-50 text-slate-800 selection:bg-[#008080] selection:text-white h-screen overflow-y-auto flex flex-col lg:flex-row">

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
            <div class="absolute top-[15%] right-[10%] w-[35%] h-[35%] rounded-full bg-rose-100/40 blur-[100px]"></div>
            <div class="absolute bottom-[10%] left-[5%] w-[30%] h-[30%] rounded-full bg-amber-100/30 blur-[80px]"></div>
        </div>

        <div class="w-full px-0 sm:px-2 py-2 sm:py-4">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 mb-1 sm:mb-2">
                User Management
            </h1>
            <p class="text-slate-500 text-sm sm:text-base mb-6 sm:mb-8">Create and manage site attendant accounts.</p>

            {{-- Messages --}}
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 text-left">
                {{-- Create User Form --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white/80 backdrop-blur-sm p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200 sticky top-4">
                        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-[#008080]"></i> Add Attendant
                        </h2>
                        <form action="{{ route('admin.attendants.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                                <input type="text" name="name" required placeholder="Full name"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                <input type="email" name="email" required placeholder="email@example.com"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all">
                            </div>
                            <div class="mb-4" x-data="{
                                open: false,
                                search: '',
                                selected: '',
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
                                    <input type="text"
                                        x-model="search"
                                        @click="open = true"
                                        @focus="open = true"
                                        @click.away="open = false; revertSearch();"
                                        @input="open = true"
                                        @keydown.escape="open = false; revertSearch();"
                                        @keydown.enter.prevent="if(filteredOptions.length > 0) selectOption(filteredOptions[0])"
                                        placeholder="Select or search an area"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all text-sm bg-white cursor-text"
                                        autocomplete="off"
                                    >
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform" 
                                       :class="open ? 'rotate-180' : ''"></i>
                                       
                                    <!-- Dropdown -->
                                    <div x-show="open" 
                                         x-transition.opacity.duration.200ms
                                         class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto"
                                         style="display: none;">
                                        <ul class="py-1">
                                            <template x-for="option in filteredOptions" :key="option.value">
                                                <li class="px-4 py-2.5 hover:bg-[#008080]/10 hover:text-[#008080] cursor-pointer text-sm text-slate-700 transition-colors"
                                                    :class="selected === option.value ? 'bg-[#008080]/10 text-[#008080] font-medium' : ''"
                                                    @click="selectOption(option)">
                                                    <span x-text="option.label"></span>
                                                </li>
                                            </template>
                                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-slate-500 text-center">
                                                No matches found
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-6" x-data="{ show: false }">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password" required
                                        placeholder="Min. 8 characters"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#008080]/20 focus:border-[#008080] outline-none transition-all pr-10">
                                    <button type="button" @click="show = !show"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                        <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full py-3 bg-[#008080] hover:bg-[#006666] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#008080]/20 active:scale-[0.98]">
                                <i class="fa-solid fa-plus mr-2"></i> Create Account
                            </button>
                        </form>
                    </div>
                </div>

                {{-- User List --}}
                <div class="lg:col-span-3">
                    <div
                        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 sm:p-6 gap-4 border-b border-slate-100">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-users text-slate-400"></i> Existing Accounts
                            </h2>

                            {{-- Search Form --}}
                            <form action="{{ route('admin.users.index') }}" method="GET" class="w-full sm:w-auto">
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search users..."
                                        class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-[#008080] outline-none transition-all text-sm">
                                    <i
                                        class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                </div>
                            </form>
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
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-slate-900 truncate">{{ $attendant->name }}</p>
                                                <p class="text-sm text-slate-500 truncate">{{ $attendant->email }}</p>
                                                <span
                                                    class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                                    <i class="fa-solid fa-map-pin mr-1 text-[10px]"></i>
                                                    {{ $attendant->dedicated_area }}
                                                </span>
                                            </div>
                                            <button type="button" 
                                                @click="deleteModal = true; deleteAction = '{{ route('admin.attendants.destroy', $attendant->id) }}'; deleteName = '{{ addslashes($attendant->name) }}'"
                                                class="text-rose-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-50 transition-all">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop Table View --}}
                            <div class="hidden lg:block overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead
                                        class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                        <tr>
                                            <th class="px-6 py-3 font-medium">Name</th>
                                            <th class="px-6 py-3 font-medium">Email</th>
                                            <th class="px-6 py-3 font-medium">Dedicated Area</th>
                                            <th class="px-6 py-3 font-medium text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($attendants as $attendant)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="px-6 py-4 font-medium text-slate-900">{{ $attendant->name }}</td>
                                                <td class="px-6 py-4 text-slate-500">{{ $attendant->email }}</td>
                                                <td class="px-6 py-4 text-slate-500">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                                                        {{ $attendant->dedicated_area }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <button type="button" 
                                                        @click="deleteModal = true; deleteAction = '{{ route('admin.attendants.destroy', $attendant->id) }}'; deleteName = '{{ addslashes($attendant->name) }}'"
                                                        class="text-rose-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-50 transition-all">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
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
            </div>
        </div>
    </main>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal" 
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" 
        x-cloak>
        <div class="card-modal" @click.away="deleteModal = false" x-show="deleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="header-modal">
                <div class="image-modal">
                    <svg aria-hidden="true" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" fill="none">
                        <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" stroke-linejoin="round" stroke-linecap="round"></path>
                    </svg>
                </div>
                <div class="content-modal">
                    <span class="title-modal">Deactivate account</span>
                    <p class="message-modal">
                        Are you sure you want to delete <span class="font-bold text-slate-800" x-text="deleteName"></span>? This action cannot be undone.
                    </p>
                </div>
                <div class="actions-modal">
                    <form :action="deleteAction" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="desactivate-modal" type="submit">Delete</button>
                    </form>
                    <button class="cancel-modal" type="button" @click="deleteModal = false">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <!-- @include('partials.footer') -->

</body>

</html>

