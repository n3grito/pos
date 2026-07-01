<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;


    protected $fillable = [
        'invoice_number', 'user_id', 'client_id', 'branch_id', 'warehouse_id',
        'cash_register_session_id', 'price_list_id', 'subtotal', 'tax', 'total',
        'amount_paid', 'change', 'payment_reference',
        'client_name', 'client_nit',
        'payment_method', 'status', 'date',
        'discount_type', 'discount_value', 'discount_amount',
        'promotion_id', 'points_earned', 'points_redeemed',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'points_earned' => 'integer',
            'points_redeemed' => 'integer',
            'date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
