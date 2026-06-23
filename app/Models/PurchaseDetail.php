<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = ['purchase_id', 'product_id', 'quantity', 'cost_price', 'subtotal'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'cost_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
