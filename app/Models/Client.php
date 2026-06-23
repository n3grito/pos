<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'document_type', 'document_number', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
