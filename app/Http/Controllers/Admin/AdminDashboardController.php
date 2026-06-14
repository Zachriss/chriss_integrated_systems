<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAssignment;
use App\Models\CashPoint;
use App\Models\DailyIncomeRecord;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\StaffTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $admin = auth()->user();
        $today = Carbon::today();

        $assignedServices = Service::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->count();

        $assignedProducts = Product::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id)
                ->where('can_manage_inventory', true);
        })->count();

        $todayCashPoint = CashPoint::where('admin_id', $admin->id)
            ->whereDate('date', $today)
            ->first();

        $todayIncome = $todayCashPoint ? $todayCashPoint->transactions()->where('type', 'income')->sum('amount') : 0;

        $assignedUsers = User::where('role', 'staff')
            ->whereHas('adminAssignments', function ($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })->count();

        $assignedServiceRequests = ServiceRequest::whereHas('service.adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->whereDate('created_at', $today)->count();

        // NEW: Staff module stats
        $todayStaffIncome = DailyIncomeRecord::whereDate('date', $today)->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $todayProfit = $todayStaffIncome - $todayExpenses;

        $monthlyStaffIncome = DailyIncomeRecord::whereMonth('date', $today->month)->whereYear('date', $today->year)->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', $today->month)->whereYear('expense_date', $today->year)->sum('amount');
        $monthlyProfit = $monthlyStaffIncome - $monthlyExpenses;

        $pendingTasks = StaffTask::where('status', 'pending')->count();
        $completedTasks = StaffTask::where('status', 'completed')->whereDate('date', $today)->count();

        // Top performing staff this month
        $topStaff = DailyIncomeRecord::with('staff')
            ->selectRaw('staff_id, SUM(amount) as total')
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->groupBy('staff_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Top services this month
        $topServices = DailyIncomeRecord::with('service')
            ->selectRaw('service_id, SUM(amount) as total, COUNT(*) as count')
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Low stock alerts
        $lowStockProducts = Product::where('quantity', '<=', 5)
            ->where('status', 'active')
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        // Service request statistics (for admin dashboard visibility)
        $pendingServiceRequests = ServiceRequest::where('status', 'pending')->count();
        $inProgressServiceRequests = ServiceRequest::where('status', 'in_progress')->count();
        $completedServiceRequests = ServiceRequest::where('status', 'completed')->count();
        $totalServiceRequests = ServiceRequest::count();

        // Recent pending service requests (orders not yet processed)
        $recentPendingRequests = ServiceRequest::with(['customer', 'service'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Recent expenses
        $recentExpenses = Expense::with('creator')
            ->orderByDesc('expense_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'todayCashPoint',
            'todayIncome',
            'todayStaffIncome',
            'todayExpenses',
            'todayProfit',
            'monthlyStaffIncome',
            'monthlyExpenses',
            'monthlyProfit',
            'assignedServices',
            'assignedProducts',
            'assignedUsers',
            'assignedServiceRequests',
            'pendingTasks',
            'completedTasks',
            'topStaff',
            'topServices',
            'lowStockProducts',
            'recentExpenses',
            'pendingServiceRequests',
            'inProgressServiceRequests',
            'completedServiceRequests',
            'totalServiceRequests',
            'recentPendingRequests'
        ));
    }
}
