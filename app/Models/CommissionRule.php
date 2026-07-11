<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    protected $fillable = [
        'provider_id',
        'agent_percentage',
        'system_percentage',
        'status',
        'created_by',
    ];

    protected $casts = [
        'agent_percentage' => 'decimal:2',
        'system_percentage' => 'decimal:2',
        'status' => 'string',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }
}