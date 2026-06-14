<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyIncomeRecord extends Model
{
    protected $fillable = [
        'staff_id',
        'task_id',
        'service_id',
        'category_id',
        'amount',
        'quantity',
        'description',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'quantity' => 'integer',
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function task()
    {
        return $this->belongsTo(StaffTask::class, 'task_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}