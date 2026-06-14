<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'category_id', 'short_description', 'description',
        'featured_image', 'gallery_images', 'base_price', 'duration_hours',
        'is_featured', 'status', 'created_by'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'duration_hours' => 'integer',
        'is_featured' => 'boolean',
        'gallery_images' => 'array',
    ];

    protected $appends = ['featured_image_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function adminAssignments()
    {
        return $this->hasMany(AdminAssignment::class);
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }
}