<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\StaffCategoryAssignment;
use Illuminate\Http\Request;

class StaffServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get category IDs assigned to this staff via category assignments
        $assignedCategoryIds = StaffCategoryAssignment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('category_id')
            ->unique();

        // Also get category IDs from tasks assigned to this staff
        $taskCategoryIds = \App\Models\StaffTask::where('staff_id', $user->id)
            ->whereNotNull('category_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('category_id')
            ->unique();

        // Merge both sources
        $allCategoryIds = $assignedCategoryIds->merge($taskCategoryIds)->unique();

        // Also get service IDs directly from tasks
        $taskServiceIds = \App\Models\StaffTask::where('staff_id', $user->id)
            ->whereNotNull('service_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('service_id')
            ->unique();

        // Get services from assigned/task categories
        $services = Service::whereIn('category_id', $allCategoryIds)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        // Also include any services directly assigned via tasks
        if ($taskServiceIds->isNotEmpty()) {
            $taskServices = Service::whereIn('id', $taskServiceIds)
                ->where('status', 'active')
                ->get();
            foreach ($taskServices as $ts) {
                if (!$services->contains('id', $ts->id)) {
                    $services->push($ts);
                }
            }
        }

        // Collect all service IDs this staff is responsible for
        $accessibleServiceIds = $services->pluck('id');

        // Group services by category
        $categories = \App\Models\ServiceCategory::whereIn('id', $allCategoryIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $grouped = [];
        foreach ($categories as $category) {
            $categoryServices = $services->where('category_id', $category->id);
            if ($categoryServices->isNotEmpty()) {
                $grouped[$category->name] = $categoryServices;
            }
        }

        // Also add uncategorized services if any
        $uncategorized = $services->whereNull('category_id');
        if ($uncategorized->isNotEmpty()) {
            $grouped['Uncategorized'] = $uncategorized;
        }

        // Get customer request counts per service for this staff
        // Include BOTH explicitly assigned requests AND unassigned requests for services this staff handles
        $serviceRequestCounts = \App\Models\ServiceRequest::where(function ($q) use ($user, $accessibleServiceIds) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhere(function ($sub) use ($accessibleServiceIds) {
                      $sub->whereNull('assigned_staff_id')
                          ->whereIn('service_id', $accessibleServiceIds);
                  });
            })
            ->selectRaw('service_id, COUNT(*) as total')
            ->groupBy('service_id')
            ->pluck('total', 'service_id');

        $categoryHasRequests = collect([]);
        foreach ($categories as $category) {
            $catServices = $services->where('category_id', $category->id)->pluck('id');
            $hasRequests = \App\Models\ServiceRequest::where(function ($q) use ($user, $catServices) {
                    $q->where('assigned_staff_id', $user->id)
                      ->orWhere(function ($sub) use ($catServices) {
                          $sub->whereNull('assigned_staff_id')
                              ->whereIn('service_id', $catServices);
                      });
                })
                ->whereIn('service_id', $catServices)
                ->whereIn('status', ['pending', 'in_progress'])
                ->exists();
            if ($hasRequests) {
                $categoryHasRequests->push($category->id);
            }
        }

        return view('staff.services', compact('grouped', 'serviceRequestCounts', 'categoryHasRequests', 'categories'));
    }

    public function servicesByCategory(Request $request, $categoryId)
    {
        $user = $request->user();
        $services = Service::where('category_id', $categoryId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($services);
    }

    public function serviceRequests(Request $request, $serviceId)
    {
        $user = $request->user();
        $service = Service::findOrFail($serviceId);

        // Include BOTH explicitly assigned requests AND unassigned requests for this service
        $serviceRequests = ServiceRequest::with(['service', 'customer'])
            ->where('service_id', $serviceId)
            ->where(function ($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereNull('assigned_staff_id');
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('staff.service-requests', compact('serviceRequests', 'service'));
    }

    public function requestShow(Request $request, $requestId)
    {
        $user = $request->user();
        $serviceRequest = ServiceRequest::with(['service', 'customer', 'assignedStaff'])
            ->findOrFail($requestId);

        // Ensure this staff has access to this request
        $accessibleServiceIds = $this->getAccessibleServiceIds($user);
        $hasAccess = $serviceRequest->assigned_staff_id === $user->id
            || ($serviceRequest->assigned_staff_id === null && $accessibleServiceIds->contains($serviceRequest->service_id));

        if (!$hasAccess) {
            abort(403, 'You do not have access to this request.');
        }

        return view('staff.service-request-show', compact('serviceRequest'));
    }

    public function markSeen(Request $request, $requestId)
    {
        $user = $request->user();
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $accessibleServiceIds = $this->getAccessibleServiceIds($user);
        $hasAccess = $serviceRequest->assigned_staff_id === $user->id
            || ($serviceRequest->assigned_staff_id === null && $accessibleServiceIds->contains($serviceRequest->service_id));

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        if (!$serviceRequest->seen_at) {
            $serviceRequest->update([
                'seen_at' => now(),
                'assigned_staff_id' => $serviceRequest->assigned_staff_id ?? $user->id,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Marked as seen.', 'seen_at' => $serviceRequest->fresh()->seen_at]);
    }

    public function respond(Request $request, $requestId)
    {
        $user = $request->user();
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $accessibleServiceIds = $this->getAccessibleServiceIds($user);
        $hasAccess = $serviceRequest->assigned_staff_id === $user->id
            || ($serviceRequest->assigned_staff_id === null && $accessibleServiceIds->contains($serviceRequest->service_id));

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'staff_response' => 'required|string|max:2000',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
        ]);

        $updateData = [
            'staff_response' => $validated['staff_response'],
            'responded_at' => now(),
            'seen_at' => $serviceRequest->seen_at ?? now(),
            'assigned_staff_id' => $serviceRequest->assigned_staff_id ?? $user->id,
        ];

        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        $serviceRequest->update($updateData);

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'service_request_response',
            'reference_id' => $serviceRequest->id,
            'description' => "Responded to service request #{$serviceRequest->id}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Response submitted successfully.',
            'request' => $serviceRequest->fresh()->load(['service', 'customer']),
        ]);
    }

    public function updateRequestStatus(Request $request, $requestId)
    {
        $user = $request->user();
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $accessibleServiceIds = $this->getAccessibleServiceIds($user);
        $hasAccess = $serviceRequest->assigned_staff_id === $user->id
            || ($serviceRequest->assigned_staff_id === null && $accessibleServiceIds->contains($serviceRequest->service_id));

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'seen_at' => $serviceRequest->seen_at ?? now(),
            'assigned_staff_id' => $serviceRequest->assigned_staff_id ?? $user->id,
        ];

        if ($validated['status'] === 'in_progress' && !$serviceRequest->responded_at) {
            $updateData['responded_at'] = now();
        }

        $serviceRequest->update($updateData);

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'service_request_status_update',
            'reference_id' => $serviceRequest->id,
            'description' => "Updated service request #{$serviceRequest->id} to {$validated['status']}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }

    /**
     * Helper to get service IDs accessible to this staff.
     */
    private function getAccessibleServiceIds($user)
    {
        $assignedCategoryIds = StaffCategoryAssignment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('category_id')
            ->unique();

        $taskCategoryIds = \App\Models\StaffTask::where('staff_id', $user->id)
            ->whereNotNull('category_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('category_id')
            ->unique();

        $allCategoryIds = $assignedCategoryIds->merge($taskCategoryIds)->unique();

        $taskServiceIds = \App\Models\StaffTask::where('staff_id', $user->id)
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

        return $accessibleServiceIds;
    }

    public function requests(Request $request)
    {
        $user = $request->user();

        // Get all service IDs this staff has access to (via category assignments or tasks)
        $assignedCategoryIds = StaffCategoryAssignment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('category_id')
            ->unique();

        $taskCategoryIds = \App\Models\StaffTask::where('staff_id', $user->id)
            ->whereNotNull('category_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('category_id')
            ->unique();

        $allCategoryIds = $assignedCategoryIds->merge($taskCategoryIds)->unique();

        $taskServiceIds = \App\Models\StaffTask::where('staff_id', $user->id)
            ->whereNotNull('service_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('service_id')
            ->unique();

        // Get services this staff can access
        $accessibleServiceIds = Service::whereIn('category_id', $allCategoryIds)
            ->where('status', 'active')
            ->pluck('id');

        if ($taskServiceIds->isNotEmpty()) {
            $taskServices = Service::whereIn('id', $taskServiceIds)
                ->where('status', 'active')
                ->pluck('id');
            $accessibleServiceIds = $accessibleServiceIds->merge($taskServices)->unique();
        }

        // Include BOTH explicitly assigned requests AND unassigned requests for accessible services
        $serviceRequests = ServiceRequest::with(['service', 'customer'])
            ->where(function ($q) use ($user, $accessibleServiceIds) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhere(function ($sub) use ($accessibleServiceIds) {
                      $sub->whereNull('assigned_staff_id')
                          ->whereIn('service_id', $accessibleServiceIds);
                  });
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('staff.service-requests', compact('serviceRequests'));
    }
}
