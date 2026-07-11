<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'cash_point_id',
        'provider_id',
        'staff_id',
        'transaction_type',
        'amount',
        'fee',
        'agent_commission',
        'system_commission',
        'reference_number',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'system_commission' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function cashPoint()
    {
        return $this->belongsTo(CashPoint::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('transaction_date', $date);
    }

    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeDeposits($query)
    {
        return $query->where('transaction_type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdraw');
    }
}