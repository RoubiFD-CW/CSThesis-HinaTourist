{{-- Global Toast Notification System --}}
<div x-data="{
        toasts: [],
        addToast(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type, show: true, progress: 100 });
            setTimeout(() => this.removeToast(id), 3000); // Give it 3s since it's an alert
        },
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 400);
            }
        },
        getIconInfo(message, type) {
            const msg = message.toLowerCase();
            // Action-based Icons matching admin.blade.php exactly
            if (msg.includes('delet') || msg.includes('remov')) return { icon: 'fa-trash-can', colors: 'text-rose-500 bg-rose-100' };
            if (msg.includes('resen') || msg.includes('email') || msg.includes('sent')) return { icon: 'fa-envelope-open-text', colors: 'text-amber-500 bg-amber-100' };
            if (msg.includes('updat') || msg.includes('edit') || msg.includes('chang') || msg.includes('profile')) return { icon: 'fa-user-pen', colors: 'text-indigo-500 bg-indigo-100' };
            if (msg.includes('save') || msg.includes('creat') || msg.includes('add')) return { icon: 'fa-check', colors: 'text-emerald-500 bg-emerald-100' };
            if (msg.includes('sync')) return { icon: 'fa-rotate', colors: 'text-teal-500 bg-teal-100' };
            
            // Fallback Type Icons
            if (type === 'error') return { icon: 'fa-xmark', colors: 'text-rose-500 bg-rose-100' };
            if (type === 'warning') return { icon: 'fa-exclamation', colors: 'text-amber-500 bg-amber-100' };
            if (type === 'info') return { icon: 'fa-info', colors: 'text-blue-500 bg-blue-100' };
            
            return { icon: 'fa-check', colors: 'text-emerald-500 bg-emerald-100' };
        }
    }"
    @notify.window="addToast($event.detail.message, $event.detail.type)"
    class="fixed top-6 right-6 z-[100] flex flex-col gap-3 items-end pointer-events-none max-w-[calc(100vw-3rem)] sm:max-w-sm">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-12"
             class="pointer-events-auto flex items-center w-full max-w-sm p-4 text-slate-500 bg-white rounded-xl shadow-lg border border-slate-200" 
             role="alert">
            
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg"
                 :class="getIconInfo(toast.message, toast.type).colors">
                <i class="fa-solid" :class="getIconInfo(toast.message, toast.type).icon"></i>
            </div>
            
            <div class="ml-4 text-sm font-normal text-slate-700 flex-1" x-text="toast.message"></div>
            
            <button type="button" @click="removeToast(toast.id)" 
                class="ms-auto -mx-1.5 -my-1.5 bg-white text-slate-400 hover:text-slate-900 rounded-lg focus:ring-2 focus:ring-slate-300 p-1.5 hover:bg-slate-100 inline-flex items-center justify-center h-8 w-8 transition-colors">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    </template>

    @if(session('success'))
        <span x-init="$nextTick(() => addToast('{{ addslashes(session('success')) }}', 'success'))" class="hidden"></span>
    @endif
    @if(session('error'))
        <span x-init="$nextTick(() => addToast('{{ addslashes(session('error')) }}', 'error'))" class="hidden"></span>
    @endif
    @if($errors->any())
        <span x-init="$nextTick(() => addToast('There are validation errors. Please check the form.', 'error'))" class="hidden"></span>
    @endif
</div>
