<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'type', 'value', 'min_amount', 'min_quantity',
        'max_discount', 'applies_to', 'start_date', 'end_date',
        'is_active', 'usage_limit', 'used_count',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    public function customerGroups(): BelongsToMany
    {
        return $this->belongsToMany(CustomerGroup::class, 'promotion_customer_group');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today());
    }

    public function scopeAvailable($query, ?float $subtotal = null, ?int $quantity = null, ?int $clientId = null)
    {
        $query = $query->active();

        if ($subtotal !== null) {
            $query->where('min_amount', '<=', $subtotal);
        }

        if ($quantity !== null) {
            $query->where(function ($q) use ($quantity) {
                $q->where('min_quantity', '<=', $quantity)->orWhere('min_quantity', 0);
            });
        }

        if ($clientId !== null) {
            $client = Client::find($clientId);
            if ($client && $client->customer_group_id) {
                $query->where(function ($q) use ($client) {
                    $q->where('applies_to', 'all')
                      ->orWhere('applies_to', 'groups');
                });
            }
        }

        if ($this->usage_limit) {
            $query->whereColumn('used_count', '<', 'usage_limit');
        }

        return $query;
    }

    public function calculateDiscount(float $subtotal): float
    {
        $discount = match ($this->type) {
            'percentage' => $subtotal * ($this->value / 100),
            'fixed' => min($this->value, $subtotal),
            default => 0,
        };

        if ($this->max_discount !== null) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return round($discount, 2);
    }
}
