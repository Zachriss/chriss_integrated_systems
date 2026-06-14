@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">My Assigned Tasks</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('staff.tasks.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>

            @if($tasks->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x display-3"></i>
                    <p class="mt-2">No tasks found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Service</th>
                                <th>Category</th>
                                <th>Assigned By</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <a href="{{ route('staff.tasks.show', $task) }}" class="text-decoration-none fw-semibold">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td>{{ $task->service->name ?? ($task->category ? 'All in ' . $task->category->name : 'N/A') }}</td>
                                <td>{{ $task->category->name ?? 'N/A' }}</td>
                                <td>{{ $task->assignedBy->name ?? 'N/A' }}</td>
                                <td>
                                    <select class="form-select form-select-sm status-toggle" style="width:130px"
                                            data-task-id="{{ $task->id }}"
                                            data-url="{{ route('staff.tasks.update-status', $task) }}">
                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($task->date)->format('M d, Y') }}</td>
                                <td>
                                     <button class="btn btn-sm btn-outline-success record-income-btn"
                                             data-task-id="{{ $task->id }}"
                                             data-task-title="{{ $task->title }}"
                                             data-service-id="{{ $task->service_id }}"
                                             data-category-id="{{ $task->category_id }}"
                                             data-has-service="{{ $task->service_id ? 'true' : 'false' }}">
                                        <i class="bi bi-currency-dollar"></i> Record
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tasks->links() }}
                </div>
            @endif
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
    // Status toggle via AJAX
    document.querySelectorAll('.status-toggle').forEach(function(select) {
        select.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const url = this.dataset.url;
            const newStatus = this.value;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Updated', text: data.message, timer: 2000, showConfirmButton: false });
                    }
                } else {
                    alert(data.message || 'Failed to update status.');
                    this.value = this.dataset.previousValue || 'pending';
                }
            })
            .catch(() => {
                alert('An error occurred.');
            });
        });
    });

    // Quick income modal
    const incomeModal = new bootstrap.Modal(document.getElementById('quickIncomeModal'));
    document.querySelectorAll('.record-income-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
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
                // Fetch services only for this task's category via AJAX
                const categoryId = this.dataset.categoryId;
                if (categoryId) {
                    modalServiceSelect.innerHTML = '<option value="">Loading...</option>';
                    fetch('{{ route("staff.services.by-category", "") }}/' + categoryId)
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
    });

    document.getElementById('quickIncomeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'success', title: 'Income Recorded', text: data.message, timer: 2000, showConfirmButton: false });
                }
                incomeModal.hide();
                form.reset();
            } else {
                alert(data.message || 'Failed to record income.');
            }
        })
        .catch(() => {
            alert('An error occurred.');
        });
    });
});
</script>
@endsection