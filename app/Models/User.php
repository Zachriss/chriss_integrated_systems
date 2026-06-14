<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'profile_image',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function auditTrails()
    {
        return $this->morphMany(AuditTrail::class, 'actor');
    }

    public function systemNotifications()
    {
        return $this->hasMany(AuditTrail::class, 'actor_id')
            ->where('actor_type', self::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function assignedServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_staff_id');
    }

    public function adminAssignments()
    {
        return $this->hasMany(AdminAssignment::class, 'admin_id');
    }

    public function cashPoints()
    {
        return $this->hasMany(CashPoint::class, 'admin_id');
    }

    public function staffTasks()
    {
        return $this->hasMany(StaffTask::class, 'staff_id');
    }

    public function assignedStaffTasks()
    {
        return $this->hasMany(StaffTask::class, 'assigned_by');
    }

    public function dailyIncomeRecords()
    {
        return $this->hasMany(DailyIncomeRecord::class, 'staff_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    public function cashpointSessions()
    {
        return $this->hasMany(CashpointSession::class, 'user_id');
    }

    public function categoryAssignments()
    {
        return $this->hasMany(StaffCategoryAssignment::class, 'user_id');
    }

    public function assignedCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'staff_category_assignments', 'user_id', 'category_id')
            ->withPivot(['status', 'notes', 'assigned_by', 'created_at'])
            ->wherePivot('status', 'active')
            ->withTimestamps();
    }

    public function categoryAssignmentsGiven()
    {
        return $this->hasMany(StaffCategoryAssignment::class, 'assigned_by');
    }
}
