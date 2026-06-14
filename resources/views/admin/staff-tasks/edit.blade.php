@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Task #{{ $task->id }}</h4>
            <p class="text-muted mb-0 small">Update task details, reassign staff, or change status</p>
        </div>
        <a href="{{ route('admin.staff-tasks.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>

    <div class="row g-4">
        {{-- MAIN FORM --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.staff-tasks.update', $task) }}" method="POST">
                        @csrf @method('PUT')

                        {{-- Staff Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1"></i> Staff Member <span class="text-danger">*</span>
                            </label>
                            <select name="staff_id" id="staffSelect" class="form-select" required>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('staff_id', $task->staff_id) == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Category Only --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i> Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="categorySelect" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-service-count="{{ $cat->services->count() }}" {{ old('category_id', $task->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->services->count() }} services)</option>
                                @endforeach
                            </select>
                            <div class="mt-2 text-muted small">
                                <i class="bi bi-info-circle me-1"></i> All services in this category will be available for income recording.
                            </div>
                        </div>

                        {{-- Task Details --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-card-heading me-1"></i> Task Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="taskTitle" class="form-control"
                                   value="{{ old('title', $task->title) }}" required maxlength="255">
                            <small class="text-muted"><span id="titleCount">{{ strlen($task->title) }}</span>/255</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-info-circle me-1"></i> Status <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusPending" value="pending"
                                        {{ old('status', $presetStatus ?? $task->status) === 'pending' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusPending">
                                        <span class="badge bg-warning">Pending</span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusInProgress" value="in_progress"
                                        {{ old('status', $presetStatus ?? $task->status) === 'in_progress' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusInProgress">
                                        <span class="badge bg-info">In Progress</span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusCompleted" value="completed"
                                        {{ old('status', $presetStatus ?? $task->status) === 'completed' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusCompleted">
                                        <span class="badge bg-success">Completed</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i> Description
                            </label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar me-1"></i> Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" class="form-control"
                                   value="{{ old('date', \Carbon\Carbon::parse($task->date)->toDateString()) }}" required>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Update Task
                            </button>
                            <a href="{{ route('admin.staff-tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SIDEBAR INFO --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-clipboard-data me-1"></i> Task Info</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-2">
                        <span class="text-muted">Created:</span>
                        <strong>{{ $task->created_at->format('M d, Y h:i A') }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Assigned By:</span>
                        <strong>{{ $task->assignedBy->name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-muted">Current Status:</span>
                        <span class="badge bg-{{ match($task->status) {
                            'pending' => 'warning',
                            'in_progress' => 'info',
                            'completed' => 'success',
                            default => 'secondary'
                        } }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Editing Tips</h6>
                </div>
                <div class="card-body small">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Changing the status helps staff know what to focus on.</li>
                        <li class="mb-2">You can reassign the task to a different staff member.</li>
                        <li>Ensure the correct category is selected for accurate reporting.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Title character count
document.getElementById('taskTitle').addEventListener('input', function(){
    document.getElementById('titleCount').textContent = this.value.length;
});
</script>
@endsection