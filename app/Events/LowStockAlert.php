<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;
    public int $stock;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->stock = $product->stock;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.notifications'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'low_stock',
            'product' => $this->product->name,
            'stock' => $this->stock,
            'min_stock' => $this->product->min_stock,
            'message' => __('Stock bajo') . ': ' . $this->product->name . ' (' . $this->stock . '/' . $this->product->min_stock . ')',
            'severity' => $this->stock <= 0 ? 'critical' : 'warning',
        ];
    }
}
