<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'read_at',
        'is_approved',
        'approved_at',
        'converted_to_testimonial',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'converted_to_testimonial' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include approved messages.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include pending (not yet approved) messages.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Approve the message and optionally convert it to a testimonial.
     */
    public function approve(): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }

    /**
     * Convert this contact message into a testimonial.
     * Returns the created Testimonial model.
     */
    public function convertToTestimonial(): Testimonial
    {
        $testimonial = Testimonial::create([
            'name' => $this->name,
            'role' => null,
            'message' => $this->message,
            'rating' => 5,
            'image' => null,
            'is_approved' => true,
            'is_active' => true,
            'order' => 0,
        ]);

        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'converted_to_testimonial' => true,
        ]);

        return $testimonial;
    }
}