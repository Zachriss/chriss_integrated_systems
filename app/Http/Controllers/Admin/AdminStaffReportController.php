<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyIncomeRecord;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStaffReportController extends Controller
{
    public function index(Request $request)
    {
        $staffMembers = User::where('role', 'staff')->orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('name')->get();
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.staff-reports.index', compact('staffMembers', 'services', 'categories'));
    }

    public function dailyIncome(Request $request)
    {
        $query = DailyIncomeRecord::with(['staff', 'service', 'category'])
            ->selectRaw('date, SUM(amount) as total_amount, COUNT(*) as record_count')
            ->groupBy('date')
            ->orderByDesc('date');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $dailyIncome = $query->paginate(20);

        $totalAmount = DailyIncomeRecord::when($request->filled('date_from'), fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->sum('amount');

        $staffMembers = User::where('role', 'staff')->orderBy('name')->get();

        return view('admin.staff-reports.daily-income', compact('dailyIncome', 'totalAmount', 'staffMembers'));
    }

    public function perStaff(Request $request)
    {
        $query = DailyIncomeRecord::with('staff')
            ->selectRaw('staff_id, SUM(amount) as total_amount, COUNT(*) as record_count')
            ->groupBy('staff_id')
            ->orderByDesc('total_amount');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $perStaff = $query->paginate(20);

        $staffMembers = User::where('role', 'staff')->orderBy('name')->get();

        return view('admin.staff-reports.per-staff', compact('perStaff', 'staffMembers'));
    }

    public function perService(Request $request)
    {
        $query = DailyIncomeRecord::with('service')
            ->selectRaw('service_id, SUM(amount) as total_amount, COUNT(*) as record_count')
            ->groupBy('service_id')
            ->orderByDesc('total_amount');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $perService = $query->paginate(20);

        $services = Service::where('status', 'active')->orderBy('name')->get();

        return view('admin.staff-reports.per-service', compact('perService', 'services'));
    }

    public function perCategory(Request $request)
    {
        $query = DailyIncomeRecord::with('category')
            ->selectRaw('category_id, SUM(amount) as total_amount, COUNT(*) as record_count')
            ->groupBy('category_id')
            ->orderByDesc('total_amount');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perCategory = $query->paginate(20);

        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.staff-reports.per-category', compact('perCategory', 'categories'));
    }

    public function staffDetail(Request $request, User $staff)
    {
        $query = DailyIncomeRecord::with(['service', 'category', 'task'])
            ->where('staff_id', $staff->id)
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $records = $query->paginate(20);

        $totalAmount = DailyIncomeRecord::where('staff_id', $staff->id)
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->sum('amount');

        return view('admin.staff-reports.staff-detail', compact('staff', 'records', 'totalAmount'));
    }
}