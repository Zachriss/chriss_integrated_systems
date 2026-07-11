<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderFeeRule extends Model
{
    protected $fillable = [
        'provider_id',
        'transaction_type',
        'min_amount',
        'max_amount',
        'fee_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'transaction_type' => 'string',
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

    public function scopeForType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }
}