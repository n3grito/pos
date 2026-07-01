<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Supplier extends Model
{
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
        'contact_person', 'is_active',
        'tax_id', 'client_type', 'photo',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
