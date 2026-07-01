<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    use HasFactory;

    const TYPES = [
        'empresa' => 'Empresa',
        'tpcp' => 'TPCP',
        'mipyme' => 'Mipyme',
        'cooperativa' => 'Cooperativa no Agropecuaria',
        'persona_natural' => 'Persona Natural',
    ];

    const TYPE_COLORS = [
        'empresa' => '#3B82F6',
        'tpcp' => '#10B981',
        'mipyme' => '#F59E0B',
        'cooperativa' => '#8B5CF6',
        'persona_natural' => '#14B8A6',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'document_type', 'document_number', 'is_active',
        'customer_group_id', 'points', 'total_spent', 'last_purchase_at', 'notes',
        'client_type', 'photo',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'points' => 'integer',
            'total_spent' => 'decimal:2',
            'last_purchase_at' => 'datetime',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->client_type] ?? $this->client_type;
    }

    public function getTypeColorAttribute(): string
    {
        return self::TYPE_COLORS[$this->client_type] ?? '#6B7280';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }
}
