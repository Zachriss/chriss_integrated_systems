<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCategoryAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'assigned_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function services()
    {
        return $this->hasManyThrough(
            Service::class,
            ServiceCategory::class,
            'id',        // service_categories.id
            'category_id', // services.category_id
            'category_id', // staff_category_assignments.category_id
            'id'          // service_categories.id
        );
    }
}