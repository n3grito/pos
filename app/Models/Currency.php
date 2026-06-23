<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'code', 'symbol', 'exchange_rate', 'is_active'];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }
}
