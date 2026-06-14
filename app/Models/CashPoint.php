<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashPoint extends Model
{
    protected $fillable = [
        'admin_id',
        'date',
        'opening_mpesa',
        'opening_airtel',
        'opening_tigo',
        'opening_halo',
        'opening_cash',
        'closing_mpesa',
        'closing_airtel',
        'closing_tigo',
        'closing_halo',
        'closing_cash',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'opening_mpesa' => 'decimal:2',
            'opening_airtel' => 'decimal:2',
            'opening_tigo' => 'decimal:2',
            'opening_halo' => 'decimal:2',
            'opening_cash' => 'decimal:2',
            'closing_mpesa' => 'decimal:2',
            'closing_airtel' => 'decimal:2',
            'closing_tigo' => 'decimal:2',
            'closing_halo' => 'decimal:2',
            'closing_cash' => 'decimal:2',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function openings()
    {
        return $this->hasMany(CashOpening::class);
    }

    public function closings()
    {
        return $this->hasMany(CashClosing::class);
    }

    public function getTotalOpeningAttribute(): float
    {
        return $this->opening_mpesa + $this->opening_airtel + $this->opening_tigo + $this->opening_halo + $this->opening_cash;
    }

    public function getTotalClosingAttribute(): float
    {
        return $this->closing_mpesa + $this->closing_airtel + $this->closing_tigo + $this->closing_halo + $this->closing_cash;
    }

    public function getCalculatedClosingAttribute(): float
    {
        $income = $this->transactions()->where('type', 'income')->sum('amount');
        $expenses = $this->transactions()->where('type', 'expense')->sum('amount');
        return $this->getTotalOpeningAttribute() + $income - $expenses;
    }
}