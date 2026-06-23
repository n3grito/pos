<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
    protected $fillable = [
        'store_name', 'company_name', 'address', 'phone',
        'logo_path', 'footer_text', 'show_seller', 'show_nit',
    ];

    protected function casts(): array
    {
        return [
            'show_seller' => 'boolean',
            'show_nit' => 'boolean',
        ];
    }
}
