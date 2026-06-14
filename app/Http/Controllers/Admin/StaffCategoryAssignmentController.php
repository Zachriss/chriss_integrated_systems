<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\StaffCategoryAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StaffCategoryAssignmentController extends Controller
{
    public function index()
    {
        $assignments = StaffCategoryAssignment::with(['staff', 'category', 'assignedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $staff = User::where('role', 'staff')->where('status', 'active')->orderBy('name')->get();
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.staff-category-assignments.index', compact('assignments', 'staff', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:service_categories,id',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Check if assignment already exists
        $existing = StaffCategoryAssignment::where('user_id', $request->user_id)
            ->where('category_id', $request->category_id)
           ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This category is already assigned to this staff member.'
            ], 409);
        }

        $assignment = StaffCategoryAssignment::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'assigned_by' => auth()->id(),
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $assignment->load(['staff', 'category', 'assignedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Category assigned successfully.',
            'assignment' => $assignment
        ]);
    }

    public function show(StaffCategoryAssignment $staffCategoryAssignment)
    {
        $staffCategoryAssignment->load(['staff', 'category', 'assignedBy']);
        return response()->json(['assignment' => $staffCategoryAssignment]);
    }

    public function update(Request $request, StaffCategoryAssignment $staffCategoryAssignment)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:service_categories,id',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Check for duplicate (excluding current record)
        $existing = StaffCategoryAssignment::where('user_id', $request->user_id)
            ->where('category_id', $request->category_id)
            ->where('id', '!=', $staffCategoryAssignment->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This category is already assigned to this staff member.'
            ], 409);
        }

        $staffCategoryAssignment->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $staffCategoryAssignment->load(['staff', 'category', 'assignedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully.',
            'assignment' => $staffCategoryAssignment
        ]);
    }

    public function destroy(StaffCategoryAssignment $staffCategoryAssignment)
    {
        $staffCategoryAssignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment removed successfully.'
        ]);
    }

    public function toggleStatus(StaffCategoryAssignment $staffCategoryAssignment)
    {
        $staffCategoryAssignment->status = $staffCategoryAssignment->status === 'active' ? 'inactive' : 'active';
        $staffCategoryAssignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Status changed to ' . $staffCategoryAssignment->status . '.',
            'status' => $staffCategoryAssignment->status
        ]);
    }

    public function getAssignments()
    {
        $assignments = StaffCategoryAssignment::with(['staff', 'category', 'assignedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['assignments' => $assignments]);
    }

    public function report()
    {
        $assignments = StaffCategoryAssignment::with(['staff', 'category', 'assignedBy'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($assignment) {
                $totalServices = $assignment->category->services()->count();
                return [
                    'id' => $assignment->id,
                    'category' => $assignment->category->name,
                    'staff_name' => $assignment->staff->name,
                    'total_services' => $totalServices,
                    'status' => $assignment->status,
                    'assigned_at' => $assignment->created_at->format('Y-m-d'),
                ];
            });

        return view('admin.staff-category-assignments.report', compact('assignments'));
    }
}