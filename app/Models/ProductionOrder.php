<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'produced_quantity', 'status',
        'notes', 'user_id', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'produced_quantity' => 'decimal:3',
            'completed_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
