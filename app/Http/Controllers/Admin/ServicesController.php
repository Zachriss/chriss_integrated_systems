<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAssignment;
use App\Models\Service;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(): View
    {
        $admin = auth()->user();

        $services = Service::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id)
                ->where('can_manage_services', true);
        })->withCount('serviceRequests')->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function show(Service $service): View
    {
        $admin = auth()->user();

        $isAssigned = AdminAssignment::where('admin_id', $admin->id)
            ->where('service_id', $service->id)
            ->where('can_manage_services', true)
            ->exists();

        abort_unless($isAssigned, 403);

        $serviceRequests = $service->serviceRequests()
            ->with(['customer', 'assignedStaff'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalIncome = $service->serviceRequests()
            ->where('status', 'completed')
            ->sum('cost');

        return view('admin.services.show', compact('service', 'serviceRequests', 'totalIncome'));
    }

    public function updateRequest(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $admin = auth()->user();

        $isAssigned = AdminAssignment::where('admin_id', $admin->id)
            ->whereHas('service.adminAssignments', function ($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })
            ->exists();

        abort_unless($isAssigned, 403);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $serviceRequest->update($request->only(['status', 'cost', 'notes']));

        return redirect()->back()->with('success', 'Service request updated successfully.');
    }

    public function incomeReport(): View
    {
        $admin = auth()->user();
        $startDate = request('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->format('Y-m-d'));

        $services = Service::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id)
                ->where('can_manage_services', true);
        })->with(['serviceRequests' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                ->where('status', 'completed');
        }])->get();

        $incomeData = $services->map(function ($service) {
            return [
                'service' => $service->name,
                'count' => $service->serviceRequests->count(),
                'income' => $service->serviceRequests->sum('cost'),
            ];
        });

        return view('admin.services.income-report', compact('incomeData', 'startDate', 'endDate'));
    }
}