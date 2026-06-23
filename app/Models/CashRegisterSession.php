<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model
{
    protected $fillable = [
        'cash_register_id', 'user_id', 'opening_balance',
        'closing_balance', 'opening_date', 'closing_date',
        'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'opening_date' => 'datetime',
            'closing_date' => 'datetime',
        ];
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
