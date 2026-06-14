<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaffActivitiesController extends Controller
{
    public function index(Request $request): View
    {
        $admin = auth()->user();

        $query = AuditTrail::where('actor_type', User::class)
            ->with('actor')
            ->orderBy('created_at', 'desc');

        // If a specific staff_id is requested, show only that staff's activities
        if ($request->filled('staff_id')) {
            $query->where('actor_id', $request->staff_id);
        } else {
            // Otherwise show all staff activities
            $staffIds = User::where('role', 'staff')->pluck('id')->toArray();
            $userIds = array_merge([$admin->id], $staffIds);
            $query->whereIn('actor_id', $userIds);
        }

        $activities = $query->paginate(30);

        return view('admin.staff-activities.index', compact('activities'));
    }

    public function staffList(): View
    {
        $staff = User::where('role', 'staff')
            ->with('staffProfile', 'categoryAssignments.category')
            ->orderBy('name')
            ->get()
            ->map(function ($staffMember) {
                // Get recent activities for each staff member
                $staffMember->recent_activities = AuditTrail::where('actor_id', $staffMember->id)
                    ->where('actor_type', User::class)
                    ->latest()
                    ->take(5)
                    ->get();
                // Count total activities
                $staffMember->total_activities = AuditTrail::where('actor_id', $staffMember->id)
                    ->where('actor_type', User::class)
                    ->count();
                return $staffMember;
            });

        return view('admin.staff-activities.staff-list', compact('staff'));
    }

    public function toggleStatus(Request $request, User $staff): RedirectResponse
    {
        if ($staff->role !== 'staff') {
            return back()->with('error', 'Can only toggle status for staff members.');
        }

        $staff->update(['status' => $staff->status === 'active' ? 'inactive' : 'active']);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'Staff Management',
            'description' => "Toggled staff status: {$staff->email} -> {$staff->status}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $action = $staff->status === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "Staff member {$action} successfully.");
    }

    public function edit(User $staff): View
    {
        if ($staff->role !== 'staff') {
            abort(404);
        }

        $staff->load('staffProfile');

        return view('admin.staff-activities.edit', compact('staff'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        if ($staff->role !== 'staff') {
            return back()->with('error', 'Can only update staff members.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $staff->phone,
            'status' => $validated['status'],
        ]);

        // Update staff profile if it exists
        if ($staff->staffProfile) {
            $staff->staffProfile->update([
                'department' => $validated['department'] ?? $staff->staffProfile->department,
                'salary' => $validated['salary'] ?? $staff->staffProfile->salary,
            ]);
        } elseif (!empty($validated['department'])) {
            $staff->staffProfile()->create([
                'department' => $validated['department'],
                'salary' => $validated['salary'] ?? 0,
            ]);
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'Staff Management',
            'description' => "Updated staff: {$staff->email}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.staff-activities.staff-list')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        if ($staff->role !== 'staff') {
            return back()->with('error', 'Can only delete staff members.');
        }

        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'delete',
            'module' => 'Staff Management',
            'description' => "Deleted staff: {$staff->email}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $staff->delete();

        return redirect()->route('admin.staff-activities.staff-list')->with('success', 'Staff member deleted successfully.');
    }
}