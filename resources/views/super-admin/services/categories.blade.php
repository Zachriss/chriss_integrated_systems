@extends('super-admin.layouts.super-admin')

@section('title', 'Service Categories')

@push('styles')
<style>
    .category-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #e8f1ff;
        color: #0d6efd;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Service Categories</h1>
        <p>Manage service categories for the marketplace.</p>
    </div>
    <button type="button" class="btn btn-sa-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" id="addCategoryBtn">
        <i class="bi bi-plus-circle me-1"></i> Add Category
    </button>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0" id="categoriesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Services</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                     @forelse($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td>
                            <div class="fw-semibold">{{ $category->name }}</div>
                            @if($category->description)
                                <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                            @endif
                        </td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td><span class="badge bg-info">{{ $category->services_count }}</span></td>
                        <td>
                            <span class="sa-badge {{ $category->is_active ? 'sa-badge-active' : 'sa-badge-inactive' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $category->createdBy?->name ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-sa-outline btn-outline-warning edit-category" 
                                        data-id="{{ $category->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-sa-outline btn-outline-danger delete-category" 
                                        data-id="{{ $category->id }}" title="Delete"><i class="bi bi-trash"></i></button>
                                <button class="btn btn-sm btn-sa-outline {{ $category->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} toggle-category" 
                                        data-id="{{ $category->id }}" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi {{ $category->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noCategoriesRow">
                        <td colspan="6" class="text-center py-4 text-muted">No categories found. Click "Add Category" to create one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="categoryForm" class="modal-content">
            @csrf
            <input type="hidden" id="categoryId" name="categoryId" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="catFormErrors" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" class="form-control" id="cat_name" name="name" required maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="cat_description" name="description" rows="2" maxlength="500"></textarea>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cat_is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="cat_is_active">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sa-primary" id="saveCategoryBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <span id="catSaveBtnText">Save Category</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCatConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category? This cannot be undone if no services are assigned.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCategoryBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // ---------- DOM references ----------
    var tbody = document.getElementById('categoriesTableBody');
    var categoryModalEl = document.getElementById('categoryModal');
    var deleteModalEl = document.getElementById('deleteCatConfirmModal');
    var addBtn = document.getElementById('addCategoryBtn');
    var catForm = document.getElementById('categoryForm');
    var catIdInput = document.getElementById('categoryId');
    var catNameInput = document.getElementById('cat_name');
    var catDescInput = document.getElementById('cat_description');
    var catActiveCheck = document.getElementById('cat_is_active');
    var saveBtn = document.getElementById('saveCategoryBtn');
    var saveBtnText = document.getElementById('catSaveBtnText');
    var confirmDelBtn = document.getElementById('confirmDeleteCategoryBtn');
    var catFormErrors = document.getElementById('catFormErrors');
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    if (!categoryModalEl || !deleteModalEl) {
        console.error('Service Categories: Modal elements missing');
        return;
    }

    var categoryModal = new bootstrap.Modal(categoryModalEl);
    var deleteModal = new bootstrap.Modal(deleteModalEl);
    var currentCatId = null;
    var isSubmitting = false;

    // ---------- Helpers ----------
    function showFormError(msg) {
        if (!catFormErrors) return;
        catFormErrors.classList.remove('d-none');
        catFormErrors.textContent = msg;
    }

    function hideFormError() {
        if (catFormErrors) catFormErrors.classList.add('d-none');
    }

    function resetForm() {
        if (catForm) catForm.reset();
        if (catIdInput) catIdInput.value = '';
        if (catActiveCheck) catActiveCheck.checked = true;
        hideFormError();
        if (saveBtn) { saveBtn.disabled = false; }
        if (saveBtnText) saveBtnText.textContent = 'Save Category';
        var spinner = saveBtn ? saveBtn.querySelector('.spinner-border') : null;
        if (spinner) spinner.classList.add('d-none');
    }

    function setSaving(loading) {
        if (!saveBtn) return;
        saveBtn.disabled = loading;
        var spinner = saveBtn.querySelector('.spinner-border');
        if (spinner) spinner.classList.toggle('d-none', !loading);
        if (saveBtnText) saveBtnText.textContent = loading ? 'Saving...' : 'Save Category';
    }

    function showNotif(message, type) {
        var container = document.getElementById('catAlertContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'catAlertContainer';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;';
            document.body.appendChild(container);
        }
        var div = document.createElement('div');
        div.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        div.style.minWidth = '280px';
        div.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        container.appendChild(div);
        setTimeout(function() { if (div.parentNode) div.remove(); }, 4000);
    }

    // ---------- Add Category button ----------
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            var label = document.getElementById('categoryModalLabel');
            if (label) label.textContent = 'Add New Category';
            resetForm();
            categoryModal.show();
        });
    }

    // ---------- Event delegation for table actions ----------
    if (tbody) {
        tbody.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn || !btn.dataset.id) return;
            var id = btn.dataset.id;

            if (btn.classList.contains('edit-category')) {
                loadCategoryForEdit(id);
            } else if (btn.classList.contains('delete-category')) {
                currentCatId = id;
                deleteModal.show();
            } else if (btn.classList.contains('toggle-category')) {
                toggleCategoryStatus(id);
            }
        });
    }

    // ---------- Load category for edit ----------
    function loadCategoryForEdit(id) {
        console.log('Loading category for edit:', id);
        var label = document.getElementById('categoryModalLabel');
        if (label) label.textContent = 'Edit Category';
        resetForm();

        fetch('/super-admin/services/categories/' + id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                console.log('Edit category response:', data);
                if (data.success) {
                    var cat = data.category;
                    if (catIdInput) catIdInput.value = cat.id;
                    if (catNameInput) catNameInput.value = cat.name || '';
                    if (catDescInput) catDescInput.value = cat.description || '';
                    if (catActiveCheck) catActiveCheck.checked = cat.is_active || false;
                    categoryModal.show();
                } else {
                    showNotif('Failed to load category', 'error');
                }
            })
            .catch(function(err) {
                console.error('Error loading category:', err);
                showNotif('Failed to load category details', 'error');
            });
    }

    // ---------- Toggle status ----------
    function toggleCategoryStatus(id) {
        console.log('Toggling status for:', id);
        fetch('/super-admin/services/categories/' + id + '/toggle-status', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            console.log('Toggle response:', data);
            if (data.success) {
                showNotif(data.message, 'success');
                var row = document.querySelector('tr[data-id="' + id + '"]');
                if (row) {
                    var badge = row.querySelector('.sa-badge');
                    if (badge) {
                        badge.textContent = data.is_active ? 'Active' : 'Inactive';
                        badge.className = 'sa-badge ' + (data.is_active ? 'sa-badge-active' : 'sa-badge-inactive');
                    }
                }
            } else {
                showNotif(data.message || 'Error updating status', 'error');
            }
        })
        .catch(function(err) {
            console.error('Toggle error:', err);
            showNotif('Network error', 'error');
        });
    }

    // ---------- Form submit (AJAX) ----------
    if (catForm) {
        catForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Category form submitted');

            if (isSubmitting) {
                console.log('Already submitting, skipping');
                return;
            }

            hideFormError();
            var id = catIdInput ? catIdInput.value : '';
            var name = catNameInput ? catNameInput.value : '';
            var description = catDescInput ? catDescInput.value : '';
            var isActive = catActiveCheck ? catActiveCheck.checked : true;

            if (!name.trim()) {
                showFormError('Category name is required.');
                return;
            }

            var formData = new FormData();
            formData.append('name', name);
            formData.append('description', description);
            formData.append('is_active', isActive ? '1' : '0');

            var url = id ? '/super-admin/services/categories/' + id : '/super-admin/services/categories';
            var method = id ? 'PUT' : 'POST';
            formData.append('_method', method);

            setSaving(true);
            isSubmitting = true;

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { status: response.status, ok: response.ok, data: data };
                });
            })
            .then(function(result) {
                console.log('Save response:', result);
                setSaving(false);
                isSubmitting = false;

                if (result.ok && result.data.success) {
                    categoryModal.hide();
                    showNotif(result.data.message, 'success');
                    resetForm();
                    // Reload the page to show updated data cleanly
                    setTimeout(function() { location.reload(); }, 600);
                } else if (result.status === 422 && result.data.errors) {
                    var msgs = [];
                    for (var f in result.data.errors) {
                        if (result.data.errors.hasOwnProperty(f)) {
                            msgs = msgs.concat(result.data.errors[f]);
                        }
                    }
                    showFormError(msgs.join(' | '));
                } else {
                    showFormError(result.data.message || 'Error saving category');
                }
            })
            .catch(function(err) {
                console.error('Save error:', err);
                setSaving(false);
                isSubmitting = false;
                showFormError('Network error. Please try again.');
            });
        });
    }

    // ---------- Delete confirmation ----------
    if (confirmDelBtn) {
        confirmDelBtn.addEventListener('click', function() {
            if (!currentCatId) return;
            console.log('Deleting category:', currentCatId);
            confirmDelBtn.disabled = true;
            confirmDelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';

            fetch('/super-admin/services/categories/' + currentCatId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                confirmDelBtn.disabled = false;
                confirmDelBtn.textContent = 'Delete';
                deleteModal.hide();
                if (data.success) {
                    showNotif(data.message, 'success');
                    var row = document.querySelector('tr[data-id="' + currentCatId + '"]');
                    if (row) row.remove();
                    // Show empty state if no rows left
                    var rows = tbody ? tbody.querySelectorAll('tr:not(#noCategoriesRow)') : [];
                    if (rows.length === 0 && tbody) {
                        tbody.innerHTML = '<tr id="noCategoriesRow"><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>';
                    }
                    currentCatId = null;
                } else {
                    showNotif(data.message || 'Error deleting', 'error');
                }
            })
            .catch(function(err) {
                console.error('Delete error:', err);
                confirmDelBtn.disabled = false;
                confirmDelBtn.textContent = 'Delete';
                deleteModal.hide();
                showNotif('Network error', 'error');
            });
        });
    }

    // ---------- Modal cleanup ----------
    if (categoryModalEl) {
        categoryModalEl.addEventListener('hidden.bs.modal', function() {
            resetForm();
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(b) { b.remove(); });
        });
    }

    console.log('Service Categories JS initialized.');
})();
</script>
@endpush