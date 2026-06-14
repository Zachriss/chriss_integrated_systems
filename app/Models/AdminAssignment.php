<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAssignment extends Model
{
    protected $fillable = [
        'admin_id',
        'service_id',
        'product_id',
        'can_manage_inventory',
        'can_manage_services',
        'can_view_reports',
        'can_manage_cash_points',
    ];

    protected function casts(): array
    {
        return [
            'can_manage_inventory' => 'boolean',
            'can_manage_services' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_manage_cash_points' => 'boolean',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }
}