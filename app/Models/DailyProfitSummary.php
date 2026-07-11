<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyProfitSummary extends Model
{
    protected $fillable = [
        'provider_id',
        'report_date',
        'total_transactions',
        'total_fees',
        'agent_profit',
        'system_profit',
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_transactions' => 'integer',
        'total_fees' => 'decimal:2',
        'agent_profit' => 'decimal:2',
        'system_profit' => 'decimal:2',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('report_date', $date);
    }

    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }
}