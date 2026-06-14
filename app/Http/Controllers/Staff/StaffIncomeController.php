<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DailyIncomeRecord;
use App\Models\Service;
use App\Models\StaffCategoryAssignment;
use App\Models\StaffTask;
use Illuminate\Http\Request;

class StaffIncomeController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();

        // If a category_id is provided, filter services by that category
        $categoryId = $request->get('category_id');

        // Get categories assigned to this staff member via category assignments
        $assignedCategoryIds = StaffCategoryAssignment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('category_id');

        // Get categories from staff tasks as well
        $taskCategories = StaffTask::with('category')
            ->where('staff_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->get()
            ->map(fn($task) => $task->category)
            ->unique('id')
            ->values();

        // Load categories from the assignment model
        $categories = \App\Models\ServiceCategory::whereIn('id', $assignedCategoryIds)
            ->where('is_active', true)
            ->get();

        // Merge with task categories (avoid duplicates)
        foreach ($taskCategories as $cat) {
            if (!$categories->contains('id', $cat->id)) {
                $categories->push($cat);
            }
        }

        // Collect all category IDs (assigned + task categories)
        $allCategoryIds = $assignedCategoryIds->toArray();
        foreach ($taskCategories as $cat) {
            if (!in_array($cat->id, $allCategoryIds)) {
                $allCategoryIds[] = $cat->id;
            }
        }

        // Get services for all available categories
        $assignedServices = Service::whereIn('category_id', $allCategoryIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Also get services from tasks that have specific services assigned
        $taskServiceIds = StaffTask::where('staff_id', $user->id)
            ->whereNotNull('service_id')
            ->whereIn('status', ['pending', 'in_progress'])
            ->pluck('service_id');

        if ($taskServiceIds->isNotEmpty()) {
            $taskServices = Service::whereIn('id', $taskServiceIds)
                ->where('status', 'active')
                ->get();

            // Merge to avoid duplicates
            foreach ($taskServices as $ts) {
                if (!$assignedServices->contains('id', $ts->id)) {
                    $assignedServices->push($ts);
                }
            }
        }

        // If filtering by category, only show services in that category
        if ($categoryId) {
            $assignedServices = $assignedServices->filter(function ($service) use ($categoryId) {
                return $service->category_id == $categoryId;
            })->values();
        }

        // Prepare services JSON for client-side filtering by category
        $servicesJson = $assignedServices->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'category_id' => $service->category_id,
            ];
        })->values();

        return view('staff.income', compact('assignedServices', 'categories', 'categoryId', 'servicesJson'));
    }

    public function edit($id)
    {
        $user = request()->user();
        $record = DailyIncomeRecord::with(['service', 'category'])
            ->where('staff_id', $user->id)
            ->findOrFail($id);

        $categoryId = $record->category_id;

        // Get categories for dropdown (assigned categories + record's own category)
        $assignedCategoryIds = StaffCategoryAssignment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('category_id')
            ->toArray();

        // Always include the record's own category, even if not in assigned list
        if ($categoryId && !in_array($categoryId, $assignedCategoryIds)) {
            $assignedCategoryIds[] = $categoryId;
        }

        $categories = \App\Models\ServiceCategory::whereIn('id', $assignedCategoryIds)
            ->where('is_active', true)
            ->get();

        // Get services for this category
        $services = Service::where('category_id', $categoryId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('staff.income-edit', compact('record', 'categories', 'services'));
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'category_id' => 'required|exists:service_categories,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:500',
            'date' => 'nullable|date',
        ]);

        $record = DailyIncomeRecord::where('staff_id', $user->id)->findOrFail($id);

        $record->update([
            'service_id' => $validated['service_id'],
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'quantity' => $validated['quantity'] ?? 1,
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'] ?? $record->date,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'income_update',
            'reference_id' => $record->id,
            'description' => "Income record #{$record->id} updated: {$record->amount} via service #{$record->service_id}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income record updated successfully.',
            'record' => $record->load(['service', 'category']),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'task_id' => 'nullable|exists:staff_tasks,id',
            'service_id' => 'required|exists:services,id',
            'category_id' => 'required|exists:service_categories,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:500',
            'date' => 'nullable|date',
        ]);

        // Verify the staff owns this task if a task_id is provided
        if (!empty($validated['task_id'])) {
            $task = StaffTask::find($validated['task_id']);
            if (!$task || $task->staff_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task selected.',
                ], 422);
            }
            // Auto-set category_id from the task
            $validated['category_id'] = $task->category_id;
            // Only auto-set service_id if the task has a specific one, otherwise allow user to select
            if ($task->service_id) {
                $validated['service_id'] = $task->service_id;
            }
        }

        $record = DailyIncomeRecord::create([
            'staff_id' => $user->id,
            'task_id' => $validated['task_id'] ?? null,
            'service_id' => $validated['service_id'],
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'quantity' => $validated['quantity'] ?? 1,
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'] ?? today()->toDateString(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'income',
            'reference_id' => $record->id,
            'description' => "Income recorded: {$record->amount} via service #{$record->service_id}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income recorded successfully.',
            'record' => $record->load(['service', 'category']),
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $query = DailyIncomeRecord::with(['service', 'category', 'task'])
            ->where('staff_id', $user->id);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Get category summaries (total amount and count per category)
        $categorySummaries = (clone $query)
            ->selectRaw('category_id, SUM(amount * quantity) as total_amount, COUNT(*) as total_records')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Grand total
        $grandTotal = $categorySummaries->sum('total_amount');

        // Get paginated records
        $records = $query->orderByDesc('date')->orderByDesc('id')->paginate(20);

        return view('staff.history', compact('records', 'categorySummaries', 'grandTotal'));
    }
}