<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_type',
        'actor_name',
        'action',
        'module',
        'description',
        'subject_id',
        'subject_type',
        'subject_name',
        'route_name',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'status_code',
        'old_values',
        'new_values',
        'metadata',
        'status',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
        ];
    }

    public function actor(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
