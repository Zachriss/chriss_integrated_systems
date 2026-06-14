<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\StaffCategoryAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTaskAssignmentController extends Controller
{
    public function index()
    {
        $assignments = StaffCategoryAssignment::with(['category', 'assignedBy'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.task-assignments.index', compact('assignments'));
    }

    public function showCategory($categoryId)
    {
        // Verify this staff member is assigned to this category
        $assignment = StaffCategoryAssignment::where('user_id', Auth::id())
            ->where('category_id', $categoryId)
            ->where('status', 'active')
            ->firstOrFail();

        // Redirect directly to income form pre-filtered for this category
        return redirect()->route('staff.income.create', ['category_id' => $categoryId]);
    }

    public function getAssignedCategories()
    {
        $assignments = StaffCategoryAssignment::with('category')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        $categories = $assignments->pluck('category');

        return response()->json(['categories' => $categories]);
    }

    public function dashboard()
    {
        $assignments = StaffCategoryAssignment::with(['category', 'assignedBy'])
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        $stats = $assignments->map(function ($assignment) {
            $totalServices = Service::where('category_id', $assignment->category_id)->count();
            $activeServices = Service::where('category_id', $assignment->category_id)
                ->where('status', 'active')
                ->count();

            return [
                'assignment' => $assignment,
                'total_services' => $totalServices,
                'active_services' => $activeServices,
            ];
        });

        return view('staff.task-assignments.dashboard', compact('assignments', 'stats'));
    }
}