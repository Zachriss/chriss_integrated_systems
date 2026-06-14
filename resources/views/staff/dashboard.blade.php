@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-primary">
                <div class="card-body p-3">
                    <h6 class="card-title text-white-50 small"><i class="bi bi-cash-stack me-1"></i> Today's Income</h6>
                    <h5 class="mb-0">TZS {{ number_format($todayIncome, 2) }}</h5>
                    <small class="text-white-50">{{ $todayCount }} record(s)</small>
                </div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-danger">
                <div class="card-body p-3">
                    <h6 class="card-title text-white-50 small"><i class="bi bi-inbox me-1"></i> Pending Orders</h6>
                    <h5 class="mb-0">{{ $pendingServiceRequests }}</h5>
                    <a href="{{ route('staff.service-requests') }}" class="text-white-50 small">View &raquo;</a>
                </div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-info">
                <div class="card-body p-3">
                    <h6 class="card-title text-dark-emphasis small"><i class="bi bi-arrow-repeat me-1"></i> In Progress</h6>
                    <h5 class="mb-0">{{ $inProgressServiceRequests }}</h5>
                    <a href="{{ route('staff.service-requests') }}" class="text-dark-emphasis small">View &raquo;</a>
                </div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-warning">
                <div class="card-body p-3">
                    <h6 class="card-title text-dark-emphasis small"><i class="bi bi-list-check me-1"></i> Pending Tasks</h6>
                    <h5 class="mb-0">{{ $pendingTasksCount }}</h5>
                    <small class="text-dark-emphasis">{{ $completedTasksCount }} completed</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart + Recent Orders Row --}}
    <div class="row g-4 mb-4">
        {{-- Bar Chart: Weekly Income --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart-fill me-1 text-primary"></i> Weekly Income (TZS)</h6>
                </div>
                <div class="card-body">
                    <canvas id="weeklyIncomeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Right: Recent Pending Orders (compact) --}}
        <div class="col-lg-7">
            @if($recentPendingRequests->isNotEmpty())
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-1 text-warning"></i> Pending Customer Orders</h6>
                    <a href="{{ route('staff.service-requests') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPendingRequests as $req)
                                @php
                                    $phone = $req->customer->phone ?? '';
                                    $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
                                    if (strlen($cleanedPhone) === 9) { $cleanedPhone = '255'.$cleanedPhone; }
                                    elseif (strlen($cleanedPhone) === 10 && str_starts_with($cleanedPhone, '0')) { $cleanedPhone = '255'.substr($cleanedPhone, 1); }
                                @endphp
                                <tr class="table-warning">
                                    <td>#{{ $req->id }}</td>
                                    <td>
                                        <strong>{{ $req->customer->full_name ?? $req->customer->name ?? 'N/A' }}</strong>
                                        @if($phone)
                                            <div class="mt-1">
                                                <a href="https://wa.me/{{ $cleanedPhone }}" target="_blank" class="btn btn-sm btn-success px-2 py-0"><i class="bi bi-whatsapp"></i></a>
                                                <a href="tel:{{ $phone }}" class="btn btn-sm btn-outline-primary px-2 py-0"><i class="bi bi-telephone"></i></a>
                                            </div>
                                        @endif
                                    </td>
                                    <td><small>{{ $req->service->name ?? 'N/A' }}</small></td>
                                    <td><small>{{ $req->created_at->format('M d, g:i A') }}</small></td>
                                    <td>
                                        <a href="{{ route('staff.service-requests.show', $req->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check-circle text-success display-4"></i>
                    <p class="mt-2 text-muted">No pending orders. All caught up!</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Support Hotline --}}
    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="mb-1"><i class="bi bi-headset me-1"></i> Customer Support Hotline</h6>
                    <span class="text-muted small">Use this number for WhatsApp/Call to respond to customer inquiries:</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="https://wa.me/255622563199" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i> +255 622 563 199
                    </a>
                    <a href="tel:+255622563199" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-telephone me-1"></i> Call
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Assigned Tasks --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check me-1"></i> Recent Assigned Tasks</h5>
            <a href="{{ route('staff.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            @if($assignedTasks->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox display-4"></i>
                    <p class="mt-2">No tasks assigned yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task</th>
                                <th>Service</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->service->name ?? 'N/A' }}</td>
                                <td>{{ $task->category->name ?? 'N/A' }}</td>
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
                                <td>{{ $task->date->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3">
        <div class="col-md-6">
            <a href="{{ route('staff.income.create') }}" class="btn btn-primary btn-lg w-100 py-3">
                <i class="bi bi-plus-circle me-2"></i> Record Income
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('staff.income.history') }}" class="btn btn-outline-secondary btn-lg w-100 py-3">
                <i class="bi bi-clock-history me-2"></i> My Income History
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyIncomeChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($weeklyLabels),
            datasets: [{
                label: 'Income (TZS)',
                data: @json($weeklyIncome),
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(13, 110, 253, 0.9)',
                    'rgba(13, 110, 253, 0.9)'
                ],
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000) return 'TZS ' + (value / 1000).toFixed(0) + 'k';
                            return 'TZS ' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection