<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'cash_point_id', 'staff_id', 'customer_id', 'from_channel_id', 'to_channel_id',
        'payment_channel_id', 'type', 'payment_method', 'transaction_type',
        'description', 'amount', 'reference', 'reference_number',
        'created_by', 'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function cashPoint()
    {
        return $this->belongsTo(CashPoint::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentChannel()
    {
        return $this->belongsTo(PaymentChannel::class, 'payment_channel_id');
    }

    public function fromChannel()
    {
        return $this->belongsTo(PaymentChannel::class, 'from_channel_id');
    }

    public function toChannel()
    {
        return $this->belongsTo(PaymentChannel::class, 'to_channel_id');
    }
}
