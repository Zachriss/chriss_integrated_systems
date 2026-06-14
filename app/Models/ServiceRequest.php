<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'customer_id',
        'service_id',
        'assigned_staff_id',
        'status',
        'cost',
        'notes',
        'problem_image_path',
        'seen_at',
        'responded_at',
        'staff_response',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'seen_at' => 'datetime',
            'responded_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
