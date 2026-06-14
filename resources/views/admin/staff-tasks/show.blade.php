@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $task->title }}</h4>
            <p class="text-muted mb-0 small">Task #{{ $task->id }} details and income records</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.staff-tasks.edit', $task) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.staff-tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- MAIN DETAILS --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">{{ $task->title }}</h5>
                            <span class="badge fs-6 bg-{{ match($task->status) {
                                'pending' => 'warning',
                                'in_progress' => 'info',
                                'completed' => 'success',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>

                    @if($task->description)
                        <div class="mb-3">
                            <small class="text-muted fw-semibold">Description</small>
                            <p class="mb-0">{{ $task->description }}</p>
                        </div>
                        <hr>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Staff</small>
                            <p class="fw-semibold mb-0">{{ $task->staff->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Service</small>
                            <p class="fw-semibold mb-0">{{ $task->service->name ?? ($task->category ? 'All services in ' . $task->category->name : 'N/A') }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Category</small>
                            <p class="fw-semibold mb-0">{{ $task->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Assigned By</small>
                            <p class="fw-semibold mb-0">{{ $task->assignedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Date</small>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($task->date)->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Created</small>
                            <p class="fw-semibold mb-0">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    {{-- Quick status update --}}
                    <hr>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted me-2">Quick update:</span>
                        <a href="{{ route('admin.staff-tasks.edit', $task) }}?status=in_progress"
                           class="btn btn-sm btn-outline-info {{ $task->status === 'in_progress' ? 'disabled' : '' }}">
                            <i class="bi bi-play me-1"></i> Mark In Progress
                        </a>
                        <a href="{{ route('admin.staff-tasks.edit', $task) }}?status=completed"
                           class="btn btn-sm btn-outline-success {{ $task->status === 'completed' ? 'disabled' : '' }}">
                            <i class="bi bi-check me-1"></i> Mark Completed
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- INCOME RECORDS --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-cash me-1"></i> Income Records</h6>
                    <span class="badge bg-secondary">{{ $task->dailyIncomeRecords->count() }} records</span>
                </div>
                <div class="card-body p-0">
                    @if($task->dailyIncomeRecords->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size:2rem;"></i>
                            <p class="text-muted small mt-2 mb-0">No income records yet.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($task->dailyIncomeRecords as $record)
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">{{ $record->date->format('M d, Y') }}</small>
                                        <br><span class="small">{{ $record->staff->name ?? 'N/A' }}</span>
                                    </div>
                                    <span class="fw-semibold text-success">TZS {{ number_format($record->amount, 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-transparent text-end">
                            <strong class="text-success">
                                Total: TZS {{ number_format($task->dailyIncomeRecords->sum('amount'), 2) }}
                            </strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection