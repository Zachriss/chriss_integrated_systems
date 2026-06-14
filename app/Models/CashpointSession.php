<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashpointSession extends Model
{
    protected $fillable = [
        'user_id',
        'opening_cash', 'opening_mpesa_float', 'opening_airtel_float', 'opening_mixx_float', 'opening_halopesa_float',
        'closing_cash', 'closing_mpesa_float', 'closing_airtel_float', 'closing_mixx_float', 'closing_halopesa_float',
        'cash_difference', 'mpesa_difference', 'airtel_difference', 'mixx_difference', 'halopesa_difference',
        'status', 'session_date', 'opened_at', 'closed_at',
    ];

    protected $casts = [
        'session_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'opening_mpesa_float' => 'decimal:2',
        'opening_airtel_float' => 'decimal:2',
        'opening_mixx_float' => 'decimal:2',
        'opening_halopesa_float' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'closing_mpesa_float' => 'decimal:2',
        'closing_airtel_float' => 'decimal:2',
        'closing_mixx_float' => 'decimal:2',
        'closing_halopesa_float' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'mpesa_difference' => 'decimal:2',
        'airtel_difference' => 'decimal:2',
        'mixx_difference' => 'decimal:2',
        'halopesa_difference' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasOpeningBalances(): bool
    {
        return $this->opening_cash > 0
            || $this->opening_mpesa_float > 0
            || $this->opening_airtel_float > 0
            || $this->opening_mixx_float > 0
            || $this->opening_halopesa_float > 0;
    }

    public function hasClosingBalances(): bool
    {
        return $this->closing_cash > 0
            || $this->closing_mpesa_float > 0
            || $this->closing_airtel_float > 0
            || $this->closing_mixx_float > 0
            || $this->closing_halopesa_float > 0;
    }

    public function getTotalOpeningFloat(): float
    {
        return $this->opening_mpesa_float
            + $this->opening_airtel_float
            + $this->opening_mixx_float
            + $this->opening_halopesa_float;
    }

    public function getTotalClosingFloat(): float
    {
        return $this->closing_mpesa_float
            + $this->closing_airtel_float
            + $this->closing_mixx_float
            + $this->closing_halopesa_float;
    }
}