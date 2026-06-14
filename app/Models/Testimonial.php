<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'role',
        'message',
        'rating',
        'image',
        'is_approved',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Scope a query to only include active and approved testimonials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_approved', true);
    }

    /**
     * Scope ordered testimonials.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderByDesc('created_at');
    }
}