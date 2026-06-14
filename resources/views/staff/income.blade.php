@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    @if($categoryId)
                        @php
                            $selectedCategory = $categories->firstWhere('id', $categoryId);
                        @endphp
                        @if($selectedCategory)
                            <h5 class="mb-0">
                                <i class="bi bi-folder2-open me-2"></i> {{ $selectedCategory->name }}
                            </h5>
                            <small class="text-muted">Record income for this category</small>
                        @else
                            <h5 class="mb-0">Record Daily Income</h5>
                        @endif
                    @else
                        <h5 class="mb-0">Record Daily Income</h5>
                    @endif
                </div>
                <div class="card-body">
                    <form id="incomeForm">
                        @csrf

                        @if($categoryId)
                            <input type="hidden" name="category_id" value="{{ $categoryId }}">
                        @else
                            <div class="mb-3">
                                <label for="categorySelect" class="form-label">Category *</label>
                                <select name="category_id" id="categorySelect" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="serviceSelect" class="form-label">Service *</label>
                            <select name="service_id" id="serviceSelect" class="form-select" required>
                                <option value="">Select Service</option>
                                @foreach($assignedServices as $service)
                                    <option value="{{ $service->id }}" data-category-id="{{ $service->category_id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amountInput" class="form-label">Amount (TZS) *</label>
                            <input type="number" name="amount" id="amountInput" class="form-control form-control-lg"
                                   step="0.01" min="0.01" placeholder="0.00" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="quantityInput" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantityInput" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="dateInput" class="form-label">Date</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ today()->toDateString() }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descInput" class="form-label">Description</label>
                            <textarea name="description" id="descInput" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-1"></i> Submit Income Record
                            </button>
                        </div>

                        <div id="resultMessage" class="mt-3" style="display:none;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.getElementById('categorySelect');
    var serviceSelect = document.getElementById('serviceSelect');
    // Get all option elements in the service dropdown
    var allServiceOptions = serviceSelect.querySelectorAll('option[data-category-id]');

    function filterServicesByCategory(categoryId) {
        for (var i = 0; i < allServiceOptions.length; i++) {
            var opt = allServiceOptions[i];
            if (!categoryId || opt.getAttribute('data-category-id') === categoryId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        }
        // If current selection is hidden, reset it
        if (serviceSelect.value && serviceSelect.selectedIndex >= 0) {
            var selectedOpt = serviceSelect.options[serviceSelect.selectedIndex];
            if (selectedOpt && selectedOpt.style.display === 'none') {
                serviceSelect.value = '';
            }
        }
    }

    if (categorySelect) {
        filterServicesByCategory(categorySelect.value);
        categorySelect.addEventListener('change', function() {
            filterServicesByCategory(this.value);
        });
    }

    document.getElementById('incomeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        if (!data.description) delete data.description;

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
            const resultDiv = document.getElementById('resultMessage');
            resultDiv.style.display = 'block';
            if (data.success) {
                resultDiv.className = 'alert alert-success mt-3';
                resultDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
                document.getElementById('incomeForm').reset();
                document.getElementById('dateInput').value = '{{ today()->toDateString() }}';
                document.getElementById('quantityInput').value = '1';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'success', title: 'Income Recorded', text: data.message, timer: 2200, showConfirmButton: false });
                }
            } else {
                resultDiv.className = 'alert alert-danger mt-3';
                resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (data.message || 'Failed to record income.');
            }
        })
        .catch(() => {
            const resultDiv = document.getElementById('resultMessage');
            resultDiv.style.display = 'block';
            resultDiv.className = 'alert alert-danger mt-3';
            resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> An error occurred. Please try again.';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Submit Income Record';
        });
    });
});
</script>
@endsection