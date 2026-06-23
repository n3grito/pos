<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'selling_price', 'tax_percentage', 'category_id', 'is_active'];

    protected $appends = ['products_json'];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getTaxAmountAttribute(): float
    {
        return round($this->selling_price * ($this->tax_percentage / 100), 2);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'service_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function getProductsJsonAttribute()
    {
        return $this->products->map(fn($p) => [
            'product_id' => $p->id,
            'name' => $p->name,
            'price' => $p->selling_price,
            'quantity' => $p->pivot->quantity,
            'stock' => $p->stock,
        ])->toJson();
    }
}
