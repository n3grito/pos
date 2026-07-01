<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sale $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->sale->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'sale_completed',
            'invoice' => $this->sale->invoice_number,
            'total' => $this->sale->total,
            'message' => __('Nueva venta') . ': ' . $this->sale->invoice_number . ' - ' . currency($this->sale->total),
        ];
    }
}
