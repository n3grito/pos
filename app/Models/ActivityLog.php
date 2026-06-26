<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'severity', 'notable',
        'description',
        'model_type', 'model_id',
        'ip_address', 'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'notable' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeNotable($query)
    {
        return $query->where('notable', true);
    }
}
