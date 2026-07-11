<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosing extends Model
{
    protected $fillable = [
        'cash_point_id',
        'provider_id',
        'closing_balance',
        'expected_balance',
        'difference',
        'closing_date',
        'recorded_by',
        'is_locked',
    ];

    protected $casts = [
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'closing_date' => 'date',
        'is_locked' => 'boolean',
    ];

    public function cashPoint()
    {
        return $this->belongsTo(CashPoint::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('closing_date', $date);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }
}