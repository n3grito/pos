<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = ['branch_id', 'name', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sessions()
    {
        return $this->hasMany(CashRegisterSession::class);
    }
}
