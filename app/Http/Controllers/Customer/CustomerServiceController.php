<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function index(Request $request)
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $query = Service::with('category')->where('status', 'active');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        $services = $query->orderBy('name')->paginate(12);
        return view('customer.services', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        if ($service->status !== 'active') {
            abort(404);
        }
        $service->load('category');
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        return view('customer.services-show', compact('service', 'categories'));
    }

    public function storeRequest(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found.'], 400);
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'description' => 'required|string|max:2000',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $serviceRequest = ServiceRequest::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'status' => 'pending',
            'notes' => $validated['description'],
            'cost' => 0,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'service_request',
            'reference_id' => $serviceRequest->id,
            'description' => "Service request #{$serviceRequest->id} for {$service->name}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service request submitted successfully.',
            'request' => $serviceRequest->load('service'),
        ]);
    }

    public function myRequests(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        $requests = $customer ? ServiceRequest::with(['service', 'assignedStaff'])
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(20) : collect();

        return view('customer.my-requests', compact('requests'));
    }
}