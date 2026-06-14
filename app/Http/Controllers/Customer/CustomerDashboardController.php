<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        $activeRequests = $customer ? ServiceRequest::where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->count() : 0;

        $completedRequests = $customer ? ServiceRequest::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->count() : 0;

        $assignedProducts = $customer ? $customer->productAssignments()->count() : 0;

        $unreadResponses = $customer ? ServiceRequest::where('customer_id', $customer->id)
            ->whereNotNull('staff_response')
            ->whereNotNull('responded_at')
            ->count() : 0;

        $recentRequests = $customer ? ServiceRequest::where('customer_id', $customer->id)
            ->with('service')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get() : collect();

        return view('customer.dashboard', compact(
            'activeRequests', 'completedRequests', 'assignedProducts', 'unreadResponses', 'recentRequests'
        ));
    }
}