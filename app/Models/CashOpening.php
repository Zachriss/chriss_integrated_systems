<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashOpening extends Model
{
    protected $fillable = [
        'cash_point_id', 'payment_channel_id', 'opening_balance',
        'opening_date', 'created_by', 'is_locked',
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

    public function paymentChannel()
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}