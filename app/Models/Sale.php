<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number', 'user_id', 'client_id', 'branch_id', 'warehouse_id',
        'cash_register_session_id', 'subtotal', 'tax', 'total',
        'amount_paid', 'change', 'payment_reference',
        'client_name', 'client_nit',
        'payment_method', 'status', 'date',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change' => 'decimal:2',
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
}
