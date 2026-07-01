<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\InventoryMovement;
use App\Models\ProductionOrder;
use App\Models\Product;
use Livewire\Component;

class StatusIndicators extends Component
{
    public int $activeSessions = 0;

    public int $lowStockCount = 0;

    public int $pendingProduction = 0;

    public int $todaySales = 0;

    public int $alerts = 0;

    public function refresh(): void
    {
        $this->activeSessions = CashRegisterSession::where('status', 'open')->count();
        $this->lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('min_stock', '>', 0)
            ->count();
        $this->pendingProduction = ProductionOrder::whereIn('status', ['pending', 'in_progress'])->count();
        $this->todaySales = \App\Models\Sale::whereDate('created_at', today())->count();
        $this->alerts = $this->lowStockCount + $this->pendingProduction;
    }

    public function render()
    {
        return view('livewire.status-indicators');
    }
}
