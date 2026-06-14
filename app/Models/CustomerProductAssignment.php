<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProductAssignment extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'assigned_by',
        'quantity',
        'status',
        'assigned_date',
    ];

    protected $casts = [
        'assigned_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}