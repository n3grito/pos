<div x-data="toastManager()" x-init="init()" class="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-80 pointer-events-none">
    <template x-for="(toast, index) in toasts" :key="index">
        <div x-show="toast.visible" x-transition:enter="transform ease-out duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transform ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0" class="pointer-events-auto rounded-xl shadow-lg px-4 py-3 text-sm font-medium border flex items-start gap-3"
            :class="{
                'bg-green-50 dark:bg-green-900/80 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200': toast.type === 'success',
                'bg-red-50 dark:bg-red-900/80 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200': toast.type === 'error',
                'bg-yellow-50 dark:bg-yellow-900/80 border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200': toast.type === 'warning',
                'bg-blue-50 dark:bg-blue-900/80 border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200': toast.type === 'info'
            }">
            <div class="flex-1 min-w-0" x-text="toast.message"></div>
            <button @click="removeToast(index)" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            init() {
                this.loadFromSession();
                document.addEventListener('livewire:navigated', () => this.loadFromSession());
            },
            loadFromSession() {
                @if(session()->has('toasts'))
                    @foreach(session('toasts') as $toast)
                        this.addToast(@json($toast['message']), @json($toast['type']), @json($toast['persistent'] ?? false));
                    @endforeach
                @endif
                @if(session('success'))
                    this.addToast(@json(session('success')), 'success');
                @endif
                @if(session('error'))
                    this.addToast(@json(session('error')), 'error');
                @endif
                @if(session('warning'))
                    this.addToast(@json(session('warning')), 'warning');
                @endif
                @if(session('info'))
                    this.addToast(@json(session('info')), 'info');
                @endif
            },
            addToast(message, type = 'success', persistent = false) {
                const toast = { message, type, visible: true };
                this.toasts.push(toast);
                if (!persistent) {
                    setTimeout(() => this.removeToast(this.toasts.indexOf(toast)), 5000);
                }
            },
            removeToast(index) {
                if (this.toasts[index]) {
                    this.toasts[index].visible = false;
                    setTimeout(() => { this.toasts.splice(index, 1); }, 300);
                }
            }
        }
    }
</script>
