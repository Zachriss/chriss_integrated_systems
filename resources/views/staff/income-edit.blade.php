@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i> Edit Income Record
                    </h5>
                    <small class="text-muted">Update the details of this income record</small>
                </div>
                <div class="card-body">
                    <form id="editIncomeForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="categorySelect" class="form-label">Category *</label>
                            <select name="category_id" id="categorySelect" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $record->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="serviceSelect" class="form-label">Service *</label>
                            <select name="service_id" id="serviceSelect" class="form-select" required>
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-category-id="{{ $service->category_id }}" {{ $record->service_id == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amountInput" class="form-label">Amount (TZS) *</label>
                            <input type="number" name="amount" id="amountInput" class="form-control form-control-lg"
                                   step="0.01" min="0.01" value="{{ $record->amount }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="quantityInput" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantityInput" class="form-control" value="{{ $record->quantity }}" min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="dateInput" class="form-label">Date</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ $record->date }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descInput" class="form-label">Description</label>
                            <textarea name="description" id="descInput" class="form-control" rows="3" placeholder="Optional notes...">{{ $record->description }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('staff.income.history') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-1"></i> Update Record
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

    document.getElementById('editIncomeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        if (!data.description) delete data.description;

        fetch('{{ route("staff.income.update", $record) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('resultMessage');
            resultDiv.style.display = 'block';
            if (data.success) {
                resultDiv.className = 'alert alert-success mt-3';
                resultDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'success', title: 'Updated', text: data.message, timer: 2200, showConfirmButton: false });
                }
                setTimeout(function() {
                    window.location.href = '{{ route("staff.income.history") }}';
                }, 1500);
            } else {
                resultDiv.className = 'alert alert-danger mt-3';
                resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (data.message || 'Failed to update record.');
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
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Record';
        });
    });
});
</script>
@endsection