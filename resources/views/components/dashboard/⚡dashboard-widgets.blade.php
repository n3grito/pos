<?php

use App\Models\UserDashboardWidget;
use Livewire\Component;

new class extends Component {
    public bool $showSettings = false;
    public array $widgets = [];
    public array $availableDefinitions = [];

    public function mount(): void
    {
        $this->availableDefinitions = UserDashboardWidget::getDefaultWidgets();
        $this->loadWidgets();
    }

    public function loadWidgets(): void
    {
        $userWidgets = UserDashboardWidget::where('user_id', auth()->id())
            ->orderBy('order')
            ->get()
            ->keyBy('widget_key');

        $this->widgets = [];
        foreach ($this->availableDefinitions as $key => $config) {
            if ($userWidgets->has($key)) {
                $uw = $userWidgets->get($key);
                $this->widgets[$key] = [
                    'enabled' => $uw->enabled,
                    'order' => $uw->order,
                ];
            } else {
                $this->widgets[$key] = [
                    'enabled' => !in_array($key, ['recent-activity', 'top-products']),
                    'order' => count($this->widgets),
                ];
            }
        }
        uasort($this->widgets, fn($a, $b) => $a['order'] <=> $b['order']);
    }

    public function toggleWidget(string $key): void
    {
        $record = UserDashboardWidget::firstOrNew(['user_id' => auth()->id(), 'widget_key' => $key]);
        $record->enabled = !$record->enabled;
        if (!$record->exists) {
            $record->order = UserDashboardWidget::where('user_id', auth()->id())->count();
        }
        $record->save();
        $this->dispatch('widgets-changed');
        $this->loadWidgets();
    }

    public function moveUp(string $key): void
    {
        $ordered = array_keys($this->widgets);
        $index = array_search($key, $ordered);
        if ($index <= 0) return;
        $this->swapOrder($ordered[$index], $ordered[$index - 1]);
    }

    public function moveDown(string $key): void
    {
        $ordered = array_keys($this->widgets);
        $index = array_search($key, $ordered);
        if ($index >= count($ordered) - 1) return;
        $this->swapOrder($ordered[$index], $ordered[$index + 1]);
    }

    protected function swapOrder(string $a, string $b): void
    {
        $records = UserDashboardWidget::where('user_id', auth()->id())
            ->whereIn('widget_key', [$a, $b])
            ->get()
            ->keyBy('widget_key');

        $orderA = $records->has($a) ? $records[$a]->order : $this->widgets[$a]['order'];
        $orderB = $records->has($b) ? $records[$b]->order : $this->widgets[$b]['order'];

        if ($records->has($a)) $records[$a]->update(['order' => $orderB]);
        if ($records->has($b)) $records[$b]->update(['order' => $orderA]);

        $this->dispatch('widgets-changed');
        $this->loadWidgets();
    }

    public function isEnabled(string $key): bool
    {
        return $this->widgets[$key]['enabled'] ?? true;
    }

    public function render()
    {
        return view('components.dashboard.dashboard-widgets');
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">{{ __('Personalizar Dashboard') }}</h3>
        <button wire:click="$toggle('showSettings')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ __('Widgets') }}
        </button>
    </div>

    @if ($showSettings)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Mostrar / Ocultar Widgets') }}</h4>
            <button wire:click="$toggle('showSettings')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-1.5">
            @foreach ($widgets as $key => $widget)
            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="w-{{ $key }}">
                <div class="flex items-center gap-2">
                    <div class="flex flex-col">
                        <button wire:click="moveUp('{{ $key }}')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 {{ $loop->first ? 'opacity-30 cursor-not-allowed' : '' }}" {{ $loop->first ? 'disabled' : '' }}>
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button wire:click="moveDown('{{ $key }}')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 {{ $loop->last ? 'opacity-30 cursor-not-allowed' : '' }}" {{ $loop->last ? 'disabled' : '' }}>
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $availableDefinitions[$key]['label'] ?? $key }}</span>
                </div>
                <button wire:click="toggleWidget('{{ $key }}')"
                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 ease-in-out focus:outline-none {{ $widget['enabled'] ? 'bg-indigo-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out {{ $widget['enabled'] ? 'translate-x-4' : 'translate-x-1' }}"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
