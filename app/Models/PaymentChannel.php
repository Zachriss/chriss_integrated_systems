<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    protected $fillable = ['name', 'code', 'type', 'status', 'created_by'];

    public function openings()
    {
        return $this->hasMany(CashOpening::class);
    }

    public function closings()
    {
        return $this->hasMany(CashClosing::class);
    }
}