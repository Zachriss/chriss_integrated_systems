<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashpointProvider extends Model
{
    protected $fillable = [
        'name',
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
}