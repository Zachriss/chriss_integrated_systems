@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('staff.tasks.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Tasks
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4>{{ $task->title }}</h4>
                        <span class="badge fs-6 bg-{{ match($task->status) {
                            'pending' => 'warning',
                            'in_progress' => 'info',
                            'completed' => 'success',
                            default => 'secondary'
                        } }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>

                    @if($task->description)
                        <p class="text-muted">{{ $task->description }}</p>
                    @endif

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted">Service</small>
                            <p class="fw-semibold">{{ $task->service->name ?? ($task->category ? 'All in ' . $task->category->name : 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Category</small>
                            <p class="fw-semibold">{{ $task->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Assigned By</small>
                            <p class="fw-semibold">{{ $task->assignedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Date</small>
                            <p class="fw-semibold">{{ \Carbon\Carbon::parse($task->date)->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Update Status</label>
                        <select class="form-select status-toggle" style="max-width:200px"
                                data-task-id="{{ $task->id }}"
                                data-url="{{ route('staff.tasks.update-status', $task) }}">
                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body text-center">
                    <h6 class="text-muted">Record Income for this Task</h6>
                    <button class="btn btn-success w-100" id="recordIncomeBtn"
                            data-task-id="{{ $task->id }}"
                            data-task-title="{{ $task->title }}"
                            data-service-id="{{ $task->service_id }}"
                            data-category-id="{{ $task->category_id }}"
                            data-has-service="{{ $task->service_id ? 'true' : 'false' }}">
                        <i class="bi bi-plus-circle me-1"></i> Add Income
                    </button>
                </div>
            </div>

            {{-- Category Services for Quick Income --}}
            @if($task->category && $task->category->services->where('status', 'active')->count() > 0)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">{{ $task->category->name }} Services</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($task->category->services->where('status', 'active') as $service)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $service->name }}</strong>
                                @if($service->base_price)
                                    <br><small class="text-muted">Price: TZS {{ number_format($service->base_price, 2) }}</small>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-outline-primary record-service-income"
                                    data-service-id="{{ $service->id }}"
                                    data-service-name="{{ $service->name }}"
                                    data-category-id="{{ $task->category_id }}"
                                    data-task-id="{{ $task->id }}">
                                <i class="bi bi-plus-circle"></i> Record
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Income History</h6>
                    <a href="{{ route('staff.income.history') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-clock-history"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($task->dailyIncomeRecords->isEmpty())
                        <p class="text-center text-muted py-3 mb-0">No income records yet.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($task->dailyIncomeRecords as $record)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">{{ $record->date->format('M d, Y') }}</small>
                                    <br><small>{{ $record->service->name ?? 'N/A' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-semibold text-success d-block">TZS {{ number_format($record->amount, 2) }}</span>
                                    <a href="{{ route('staff.income.edit', $record) }}" class="btn btn-sm btn-outline-primary mt-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Income Modal --}}
<div class="modal fade" id="quickIncomeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Income for: <span id="quickIncomeTaskTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickIncomeForm">
                <input type="hidden" id="quickIncomeTaskId" name="task_id">
                <input type="hidden" id="quickIncomeServiceId" name="service_id">
                <input type="hidden" id="quickIncomeCategoryId" name="category_id">
                <div class="modal-body">
                    <div class="mb-3" id="modalServiceSelectWrapper" style="display:none;">
                        <label class="form-label">Service *</label>
                        <select name="service_id" id="modalServiceSelect" class="form-select" required>
                            <option value="">Select Service</option>
                            <option disabled>--- Loading services from the task's category ---</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (TZS)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Income</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status toggle
    const statusToggle = document.querySelector('.status-toggle');
    if (statusToggle) {
        statusToggle.addEventListener('change', function() {
            fetch(this.dataset.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: this.value })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed');
                }
            });
        });
    }

    // Quick income from main button
    const incomeModal = new bootstrap.Modal(document.getElementById('quickIncomeModal'));
    const mainBtn = document.getElementById('recordIncomeBtn');
    if (mainBtn) {
        mainBtn.addEventListener('click', function() {
            document.getElementById('quickIncomeTaskId').value = this.dataset.taskId;
            document.getElementById('quickIncomeServiceId').value = this.dataset.serviceId;
            document.getElementById('quickIncomeCategoryId').value = this.dataset.categoryId;
            document.getElementById('quickIncomeTaskTitle').textContent = this.dataset.taskTitle;
            
            const hasService = this.dataset.hasService === 'true';
            const serviceWrapper = document.getElementById('modalServiceSelectWrapper');
            const modalServiceSelect = document.getElementById('modalServiceSelect');
            
            if (!hasService) {
                serviceWrapper.style.display = 'block';
                modalServiceSelect.required = true;
                const categoryId = this.dataset.categoryId;
                if (categoryId) {
                    modalServiceSelect.innerHTML = '<option value="">Loading...</option>';
                    fetch('{{ route("staff.services.by-category", ["categoryId" => "__CATEGORY__"]) }}'.replace('__CATEGORY__', categoryId))
                        .then(function(r) { return r.json(); })
                        .then(function(services) {
                            modalServiceSelect.innerHTML = '<option value="">Select Service</option>';
                            services.forEach(function(svc) {
                                var opt = document.createElement('option');
                                opt.value = svc.id;
                                opt.textContent = svc.name;
                                modalServiceSelect.appendChild(opt);
                            });
                        })
                        .catch(function() {
                            modalServiceSelect.innerHTML = '<option value="">Failed to load services</option>';
                        });
                }
            } else {
                serviceWrapper.style.display = 'none';
                modalServiceSelect.required = false;
            }
            
            incomeModal.show();
        });
    }

    // Quick income from specific service buttons
    document.querySelectorAll('.record-service-income').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const serviceId = this.dataset.serviceId;
            const serviceName = this.dataset.serviceName;
            const categoryId = this.dataset.categoryId;
            
            document.getElementById('quickIncomeTaskId').value = taskId;
            document.getElementById('quickIncomeServiceId').value = serviceId;
            document.getElementById('quickIncomeCategoryId').value = categoryId;
            document.getElementById('quickIncomeTaskTitle').textContent = serviceName;
            
            // Hide service dropdown since we already have the service selected
            const serviceWrapper = document.getElementById('modalServiceSelectWrapper');
            if (serviceWrapper) {
                serviceWrapper.style.display = 'none';
            }
            
            incomeModal.show();
        });
    });

    document.getElementById('quickIncomeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // If modal service select is visible, use its value
        const modalServiceSelect = document.getElementById('modalServiceSelect');
        if (modalServiceSelect && modalServiceSelect.closest('#modalServiceSelectWrapper').style.display !== 'none') {
            data.service_id = modalServiceSelect.value;
        }
        
        fetch('{{ route("staff.income.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed');
            }
        });
    });
});
</script>
@endsection