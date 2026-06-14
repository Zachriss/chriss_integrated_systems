<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffTask extends Model
{
    protected $fillable = [
        'staff_id',
        'service_id',
        'category_id',
        'title',
        'description',
        'status',
        'assigned_by',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function dailyIncomeRecords()
    {
        return $this->hasMany(DailyIncomeRecord::class, 'task_id');
    }
}