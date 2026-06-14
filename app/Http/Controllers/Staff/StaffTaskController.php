<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\StaffTask;
use Illuminate\Http\Request;

class StaffTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = StaffTask::with(['service', 'category', 'assignedBy'])
            ->where('staff_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(15);

        return view('staff.tasks', compact('tasks'));
    }

    public function show(Request $request, StaffTask $task)
    {
        $user = $request->user();

        if ($task->staff_id !== $user->id) {
            abort(403, 'You can only view your own tasks.');
        }

        $task->load(['service', 'category', 'assignedBy', 'dailyIncomeRecords']);

        return view('staff.tasks-show', compact('task'));
    }

    public function updateStatus(Request $request, StaffTask $task)
    {
        $user = $request->user();

        if ($task->staff_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $validated['status']]);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'task_update',
            'reference_id' => $task->id,
            'description' => "Task #{$task->id} status changed to {$task->status}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'task' => $task->only(['id', 'status']),
        ]);
    }
}