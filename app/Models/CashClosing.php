<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosing extends Model
{
    protected $fillable = [
        'cash_point_id', 'payment_channel_id', 'closing_balance',
        'expected_balance', 'difference', 'closing_date', 'recorded_by',
    ];

    protected $casts = [
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'closing_date' => 'date',
    ];

    public function cashPoint()
    {
        return $this->belongsTo(CashPoint::class);
    }

    public function paymentChannel()
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}