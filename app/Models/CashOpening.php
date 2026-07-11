<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashOpening extends Model
{
    protected $fillable = [
        'cash_point_id',
        'provider_id',
        'opening_balance',
        'opening_date',
        'is_locked',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_date' => 'date',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('opening_date', $date);
    }
}