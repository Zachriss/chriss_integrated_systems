<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StaffTask;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStaffTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffTask::with(['staff', 'service', 'category', 'assignedBy'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('staff', fn($qq) => $qq->where('name', 'like', "%{$s}%"));
            });
        }

        $tasks = $query->paginate(15)->withQueryString();
        $staffMembers = User::where('role', 'staff')->orderBy('name')->get();

        // Stats for cards
        $totalPending = StaffTask::where('status', 'pending')->count();
        $totalInProgress = StaffTask::where('status', 'in_progress')->count();
        $totalCompleted = StaffTask::where('status', 'completed')->count();
        $totalTasks = StaffTask::count();

        // Staff workload
        $staffWorkload = User::where('role', 'staff')->where('status', 'active')
            ->withCount(['staffTasks as pending_count' => fn($q) => $q->where('status', 'pending')])
            ->withCount(['staffTasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress')])
            ->orderBy('pending_count', 'desc')
            ->get();

        return view('admin.staff-tasks.index', compact(
            'tasks', 'staffMembers',
            'totalPending', 'totalInProgress', 'totalCompleted', 'totalTasks',
            'staffWorkload'
        ));
    }

    public function create()
    {
        $staffMembers = User::where('role', 'staff')->where('status', 'active')->orderBy('name')->get();
        $categories = ServiceCategory::with('services')->where('is_active', true)->orderBy('name')->get();

        return view('admin.staff-tasks.create', compact('staffMembers', 'categories'));
    }

    public function store(Request $request)
    {
        $admin = $request->user();

        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'date' => 'required|date',
        ]);

        $task = StaffTask::create([
            'staff_id' => $validated['staff_id'],
            'service_id' => $validated['service_id'] ?? null,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
            'assigned_by' => $admin->id,
            'date' => $validated['date'],
        ]);

        return redirect()->route('admin.staff-tasks.index')
            ->with('success', 'Task assigned successfully.');
    }

    public function show(StaffTask $task)
    {
        $task->load(['staff', 'service', 'category', 'assignedBy', 'dailyIncomeRecords']);
        return view('admin.staff-tasks.show', compact('task'));
    }

    public function edit(Request $request, StaffTask $task)
    {
        $staffMembers = User::where('role', 'staff')->where('status', 'active')->orderBy('name')->get();
        $categories = ServiceCategory::with('services')->where('is_active', true)->orderBy('name')->get();
        $presetStatus = $request->query('status');

        return view('admin.staff-tasks.edit', compact('task', 'staffMembers', 'categories', 'presetStatus'));
    }

    public function update(Request $request, StaffTask $task)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:pending,in_progress,completed',
            'date' => 'required|date',
        ]);

        $task->update($validated);

        return redirect()->route('admin.staff-tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, StaffTask $task)
    {
        $task->delete();

        return redirect()->route('admin.staff-tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}