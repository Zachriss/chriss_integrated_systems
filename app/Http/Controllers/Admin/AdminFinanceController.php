<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyIncomeRecord;
use App\Models\Expense;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class AdminFinanceController extends Controller
{
    public function profitLoss(Request $request)
    {
        $dateFrom = $request->input('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());

        $totalIncome = DailyIncomeRecord::whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $profit = $totalIncome - $totalExpenses;

        $dailyIncome = DailyIncomeRecord::selectRaw('date, SUM(amount) as total_income')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyExpenses = Expense::selectRaw('expense_date as date, SUM(amount) as total_expense')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get();

        // Per-service breakdown
        $perService = DailyIncomeRecord::with('service')
            ->selectRaw('service_id, SUM(amount) as total')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->get();

        // Per-category expense breakdown
        $perExpenseCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.finance.profit-loss', compact(
            'dateFrom', 'dateTo',
            'totalIncome', 'totalExpenses', 'profit',
            'dailyIncome', 'dailyExpenses',
            'perService', 'perExpenseCategory'
        ));
    }

    public function financialReport(Request $request)
    {
        $dateFrom = $request->input('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());

        $totalIncome = DailyIncomeRecord::whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $profit = $totalIncome - $totalExpenses;

        $incomeByCategory = DailyIncomeRecord::with('category')
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $incomeByStaff = DailyIncomeRecord::with('staff')
            ->selectRaw('staff_id, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('staff_id')
            ->orderByDesc('total')
            ->get();

        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('name')->get();

        return view('admin.finance.financial-report', compact(
            'dateFrom', 'dateTo',
            'totalIncome', 'totalExpenses', 'profit',
            'incomeByCategory', 'expensesByCategory', 'incomeByStaff',
            'categories', 'services'
        ));
    }
}