<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DailyIncomeRecord;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\StaffCategoryAssignment;
use App\Models\StaffTask;
use Illuminate\Http\Request;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $assignedTasks = StaffTask::with(['service', 'category'])
            ->where('staff_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $todayIncome = DailyIncomeRecord::where('staff_id', $user->id)
            ->whereDate('date', today())
            ->sum('amount');

        $todayCount = DailyIncomeRecord::where('staff_id', $user->id)
            ->whereDate('date', today())
            ->count();

        $pendingTasksCount = StaffTask::where('staff_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $inProgressTasksCount = StaffTask::where('staff_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        $completedTasksCount = StaffTask::where('staff_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Get accessible service IDs for this staff (same logic as StaffServiceController)
        $assignedCategoryIds = StaffCategoryAssignment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('category_id')
            ->unique();

        $taskCategoryIds = StaffTask::where('staff_id', $user->id)
            ->whereNotNull('category_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('category_id')
            ->unique();

        $allCategoryIds = $assignedCategoryIds->merge($taskCategoryIds)->unique();

        $taskServiceIds = StaffTask::where('staff_id', $user->id)
            ->whereNotNull('service_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('service_id')
            ->unique();

        $accessibleServiceIds = Service::whereIn('category_id', $allCategoryIds)
            ->where('status', 'active')
            ->pluck('id');

        if ($taskServiceIds->isNotEmpty()) {
            $taskServices = Service::whereIn('id', $taskServiceIds)
                ->where('status', 'active')
                ->pluck('id');
            $accessibleServiceIds = $accessibleServiceIds->merge($taskServices)->unique();
        }

        // Service request counts for this staff's accessible services
        $pendingServiceRequests = ServiceRequest::where(function ($q) use ($user, $accessibleServiceIds) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhere(function ($sub) use ($accessibleServiceIds) {
                      $sub->whereNull('assigned_staff_id')
                          ->whereIn('service_id', $accessibleServiceIds);
                  });
            })
            ->where('status', 'pending')
            ->count();

        $inProgressServiceRequests = ServiceRequest::where(function ($q) use ($user, $accessibleServiceIds) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhere(function ($sub) use ($accessibleServiceIds) {
                      $sub->whereNull('assigned_staff_id')
                          ->whereIn('service_id', $accessibleServiceIds);
                  });
            })
            ->where('status', 'in_progress')
            ->count();

        $completedServiceRequestsToday = ServiceRequest::where(function ($q) use ($user, $accessibleServiceIds) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhere(function ($sub) use ($accessibleServiceIds) {
                      $sub->whereNull('assigned_staff_id')
                          ->whereIn('service_id', $accessibleServiceIds);
                  });
            })
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        // Weekly income data for chart (last 7 days)
        $weeklyIncome = collect([]);
        $weeklyLabels = collect([]);
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyLabels->push($date->format('D'));
            $dayIncome = DailyIncomeRecord::where('staff_id', $user->id)
                ->whereDate('date', $date)
                ->sum('amount');
            $weeklyIncome->push((float) $dayIncome);
        }

        // Recent pending service requests for this staff's services
        $recentPendingRequests = ServiceRequest::with(['customer', 'service'])
            ->where(function ($q) use ($user, $accessibleServiceIds) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhere(function ($sub) use ($accessibleServiceIds) {
                      $sub->whereNull('assigned_staff_id')
                          ->whereIn('service_id', $accessibleServiceIds);
                  });
            })
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact(
            'assignedTasks',
            'todayIncome',
            'todayCount',
            'pendingTasksCount',
            'inProgressTasksCount',
            'completedTasksCount',
            'pendingServiceRequests',
            'inProgressServiceRequests',
            'completedServiceRequestsToday',
            'recentPendingRequests',
            'accessibleServiceIds',
            'weeklyIncome',
            'weeklyLabels'
        ));
    }
}