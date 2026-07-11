<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function feeRules()
    {
        return $this->hasMany(ProviderFeeRule::class);
    }

    public function commissionRules()
    {
        return $this->hasMany(CommissionRule::class);
    }

    public function cashOpenings()
    {
        return $this->hasMany(CashOpening::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function cashClosings()
    {
        return $this->hasMany(CashClosing::class);
    }
}