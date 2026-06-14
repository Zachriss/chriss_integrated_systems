@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Staff Task Management</h4>
            <p class="text-muted mb-0 small">Assign, track, and manage staff tasks efficiently</p>
        </div>
        <a href="{{ route('admin.staff-tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Assign New Task
        </a>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i class="bi bi-list-task fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Tasks</div>
                            <h4 class="mb-0">{{ $totalTasks }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Pending</div>
                            <h4 class="mb-0">{{ $totalPending }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-3">
                            <i class="bi bi-arrow-repeat fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">In Progress</div>
                            <h4 class="mb-0">{{ $totalInProgress }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                            <i class="bi bi-check2-all fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Completed</div>
                            <h4 class="mb-0">{{ $totalCompleted }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STAFF WORKLOAD OVERVIEW --}}
    @if($staffWorkload->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-people me-1"></i> Staff Workload Overview</h6>
            <small class="text-muted">Active staff members & their pending tasks</small>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($staffWorkload as $staff)
                <div class="col-md-3 col-6">
                    <div class="d-flex align-items-center justify-content-between border rounded p-2 {{ $staff->pending_count > 3 ? 'border-warning bg-warning bg-opacity-10' : 'border-light' }}">
                        <div class="text-truncate me-2">
                            <div class="fw-semibold small text-truncate">{{ $staff->name }}</div>
                            <small class="text-muted">
                                @if($staff->pending_count > 0)
                                    <span class="text-warning fw-bold">{{ $staff->pending_count }} pending</span>
                                @else
                                    <span class="text-success">Clear</span>
                                @endif
                                @if($staff->in_progress_count > 0)
                                    <span class="text-info ms-1">| {{ $staff->in_progress_count }} active</span>
                                @endif
                            </small>
                        </div>
                        <a href="{{ route('admin.staff-tasks.index', ['staff_id' => $staff->id]) }}"
                           class="btn btn-sm btn-outline-secondary rounded-circle p-1"
                           title="View {{ $staff->name }}'s tasks"
                           style="width:28px;height:28px;">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- TASKS LIST --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            {{-- FILTERS & SEARCH --}}
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search tasks or staff..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="staff_id" class="form-select form-select-sm">
                        <option value="">All Staff</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('admin.staff-tasks.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($tasks->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size:3rem;"></i>
                    <h5 class="mt-2 text-muted">No tasks found</h5>
                    <p class="text-muted small">Create a new task assignment to get started.</p>
                    <a href="{{ route('admin.staff-tasks.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Assign New Task
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Task</th>
                                <th>Staff</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td class="text-muted small">#{{ $task->id }}</td>
                                <td>
                                    <a href="{{ route('admin.staff-tasks.show', $task) }}" class="text-decoration-none fw-semibold text-dark">
                                        {{ $task->title }}
                                    </a>
                                    @if($task->description)
                                        <br><small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $task->staff->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($task->category)
                                        <span class="fw-semibold">{{ $task->category->name }}</span>
                                        <br><small class="text-muted">{{ $task->service->name ?? 'All services' }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($task->status) {
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        default => 'secondary'
                                    } }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <small>{{ \Carbon\Carbon::parse($task->date)->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.staff-tasks.show', $task) }}" class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.staff-tasks.edit', $task) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.staff-tasks.destroy', $task) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete task #{{ $task->id }}? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Showing {{ $tasks->firstItem() ?? 0 }}–{{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() }} tasks</small>
                    <div>{{ $tasks->appends(request()->query())->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection