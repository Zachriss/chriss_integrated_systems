<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashPoint extends Model
{
    protected $fillable = [
        'name',
        'status',
        'admin_id',
        'date',
    ];

    protected $casts = [
        'status' => 'string',
        'date' => 'date',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function cashOpenings()
    {
        return $this->hasMany(CashOpening::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class, 'cash_point_id');
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function cashClosings()
    {
        return $this->hasMany(CashClosing::class);
    }

    public function staffAssignments()
    {
        return $this->hasMany(StaffCashAssignment::class);
    }

    public function assignedStaff()
    {
        return $this->belongsToMany(User::class, 'staff_cash_assignments', 'cash_point_id', 'staff_id')
            ->withPivot('status', 'start_date', 'end_date')
            ->wherePivot('status', 'active');
    }
}