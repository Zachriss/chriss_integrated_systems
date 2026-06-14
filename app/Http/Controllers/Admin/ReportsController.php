<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashPoint;
use App\Models\CashTransaction;
use App\Models\Service;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function dailyCash(): View
    {
        $admin = auth()->user();
        $date = request('date', Carbon::today()->format('Y-m-d'));

        $cashPoint = CashPoint::where('admin_id', $admin->id)
            ->whereDate('date', $date)
            ->with('transactions.createdBy')
            ->first();

        return view('admin.reports.daily-cash', compact('cashPoint', 'date'));
    }

    public function transactionReport(): View
    {
        $admin = auth()->user();
        $startDate = request('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->format('Y-m-d'));
        $paymentMethod = request('payment_method');

        $transactions = CashTransaction::whereHas('cashPoint', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['cashPoint', 'createdBy'])
            ->orderBy('created_at', 'desc');

        if ($paymentMethod) {
            $transactions->where('payment_method', $paymentMethod);
        }

        $transactions = $transactions->paginate(30);

        $totals = [
            'income' => $transactions->where('type', 'income')->sum('amount'),
            'expenses' => $transactions->where('type', 'expense')->sum('amount'),
        ];

        return view('admin.reports.transactions', compact(
            'transactions',
            'totals',
            'startDate',
            'endDate',
            'paymentMethod'
        ));
    }

    public function servicePerformance(): View
    {
        $admin = auth()->user();
        $startDate = request('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->format('Y-m-d'));

        $services = Service::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id)
                ->where('can_manage_services', true);
        })->with(['serviceRequests' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }])->get();

        $performanceData = $services->map(function ($service) {
            $requests = $service->serviceRequests;
            return [
                'service' => $service->name,
                'category' => $service->category,
                'total_requests' => $requests->count(),
                'completed' => $requests->where('status', 'completed')->count(),
                'pending' => $requests->where('status', 'pending')->count(),
                'total_income' => $requests->sum('cost'),
            ];
        });

        return view('admin.reports.service-performance', compact(
            'performanceData',
            'startDate',
            'endDate'
        ));
    }
}