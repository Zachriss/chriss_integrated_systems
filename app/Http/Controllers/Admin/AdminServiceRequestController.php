<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class AdminServiceRequestController extends Controller
{
    /**
     * Display all service requests (orders) for admin.
     */
    public function index(Request $request)
    {
        $admin = auth()->user();
        $query = ServiceRequest::with(['customer', 'service', 'assignedStaff', 'processedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Search by customer name, phone, or request ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($sub) use ($search) {
                      $sub->where('full_name', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $serviceRequests = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Get counts for status badges
        $counts = [
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
            'total' => ServiceRequest::count(),
        ];

        // Get services for filter dropdown
        $services = \App\Models\Service::where('status', 'active')->orderBy('name')->get();

        return view('admin.service-requests.index', compact('serviceRequests', 'counts', 'services'));
    }

    /**
     * Show a single service request details.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['customer', 'service', 'assignedStaff', 'processedBy']);
        return view('admin.service-requests.show', compact('serviceRequest'));
    }

    /**
     * Update service request (status, cost, notes, assignment).
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $admin = auth()->user();

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:5000',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'staff_response' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ];

        if (isset($validated['cost'])) {
            $updateData['cost'] = $validated['cost'];
        }

        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        if (isset($validated['assigned_staff_id'])) {
            $updateData['assigned_staff_id'] = $validated['assigned_staff_id'];
        }

        if (isset($validated['staff_response']) && !empty($validated['staff_response'])) {
            $updateData['staff_response'] = $validated['staff_response'];
            $updateData['responded_at'] = $serviceRequest->responded_at ?? now();
        }

        // Auto set seen when admin processes
        if (!$serviceRequest->seen_at) {
            $updateData['seen_at'] = now();
        }

        $serviceRequest->update($updateData);

        ActivityLog::create([
            'user_id' => $admin->id,
            'role' => $admin->role,
            'action_type' => 'admin_service_request_update',
            'reference_id' => $serviceRequest->id,
            'description' => "Admin updated service request #{$serviceRequest->id} to {$validated['status']}",
            'date' => today(),
        ]);

        return redirect()->route('admin.service-requests.show', $serviceRequest)
            ->with('success', 'Service request updated successfully.');
    }

    /**
     * Get all staff members for assignment dropdown.
     */
    public function staffList()
    {
        $staff = \App\Models\User::where('role', 'staff')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($staff);
    }
}