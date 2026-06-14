<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'action_type',
        'reference_id',
        'description',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}