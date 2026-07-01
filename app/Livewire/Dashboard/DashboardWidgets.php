<?php

namespace App\Livewire\Dashboard;

use App\Models\UserDashboardWidget;
use Livewire\Component;

class DashboardWidgets extends Component
{
    public array $widgets = [];

    public function mount(): void
    {
        $this->loadWidgets();
    }

    public function loadWidgets(): void
    {
        $definitions = UserDashboardWidget::getDefaultWidgets();
        $userWidgets = UserDashboardWidget::where('user_id', auth()->id())
            ->get()
            ->keyBy('widget_key');

        $this->widgets = [];
        foreach ($definitions as $key => $config) {
            $this->widgets[$key] = [
                'label' => $config['label'],
                'description' => $config['description'],
                'icon' => $config['icon'],
                'enabled' => $userWidgets->has($key)
                    ? $userWidgets->get($key)->enabled
                    : !in_array($key, ['recent-activity', 'top-products']),
            ];
        }
    }

    public function toggle(string $widgetKey): void
    {
        $widget = UserDashboardWidget::firstOrCreate(
            ['user_id' => auth()->id(), 'widget_key' => $widgetKey],
            ['enabled' => !in_array($widgetKey, ['recent-activity', 'top-products']), 'order' => 0]
        );
        $widget->update(['enabled' => !$widget->enabled]);

        $this->loadWidgets();
        $this->dispatch('widgets-toggled');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-widgets');
    }
}
