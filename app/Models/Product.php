<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (!$product->sku) {
                $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $product->name), 0, 3));
                $last = static::where('sku', 'like', "$prefix-%")->max('sku');
                $next = $last ? (int) substr($last, -3) + 1 : 1;
                $product->sku = $prefix . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'name', 'sku', 'barcode', 'description', 'category_id',
        'branch_id', 'unit_id', 'cost_price', 'selling_price', 'tax_percentage',
        'stock', 'min_stock', 'is_active', 'available_for_sale',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'stock' => 'decimal:3',
            'min_stock' => 'decimal:3',
            'is_active' => 'boolean',
            'available_for_sale' => 'boolean',
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function priceLists()
    {
        return $this->belongsToMany(PriceList::class, 'product_prices')->withPivot('price');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }

    public function getPriceForList(?PriceList $priceList): float
    {
        if (!$priceList) {
            return (float) $this->selling_price;
        }
        $pivot = $this->priceLists()->where('price_list_id', $priceList->id)->first();
        return $pivot ? (float) $pivot->pivot->price : (float) $this->selling_price;
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
