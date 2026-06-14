@extends('super-admin.layouts.super-admin')

@section('title', 'Services Management')

@push('styles')
<style>
    .service-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }
    .service-image-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        color: #64748b;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .featured-badge {
        background: #fef3c7;
        color: #92400e;
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Services Management</h1>
        <p>Manage all system services with dynamic modal forms.</p>
    </div>
    <button type="button" class="btn btn-sa-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" id="addServiceBtn">
        <i class="bi bi-plus-circle me-1"></i> Add Service
    </button>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0" id="servicesTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="servicesTableBody">
                     @forelse($services as $service)
                    <tr data-id="{{ $service->id }}">
                        <td>
                            @if($service->featured_image_url)
                                <img src="{{ $service->featured_image_url }}" 
                                     alt="{{ $service->name }}" class="service-image">
                            @else
                                <div class="service-image-wrapper">
                                    {{ strtoupper(substr($service->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $service->name }}</div>
                            @if($service->short_description)
                                <small class="text-muted">{{ Str::limit($service->short_description, 40) }}</small>
                            @endif
                        </td>
                        <td>{{ $service->category?->name ?? '-' }}</td>
                        <td>
                            <span class="sa-badge {{ ($service->status ?? 'active') === 'active' ? 'sa-badge-active' : 'sa-badge-inactive' }}">
                                {{ $service->status ?? 'active' }}
                            </span>
                        </td>
                        <td>
                            @if($service->is_featured ?? false)
                                <span class="featured-badge"><i class="bi bi-star-fill me-1"></i> Featured</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $service->createdBy?->name ?? '—' }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $service->created_at?->format('d M Y') ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-sa-outline btn-outline-primary view-service" 
                                        data-id="{{ $service->id }}" title="View"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-sa-outline btn-outline-warning edit-service" 
                                        data-id="{{ $service->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-sa-outline btn-outline-danger delete-service" 
                                        data-id="{{ $service->id }}" title="Delete"><i class="bi bi-trash"></i></button>
                                <button class="btn btn-sm btn-sa-outline {{ ($service->status ?? 'active') === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }} toggle-status" 
                                        data-id="{{ $service->id }}" title="{{ ($service->status ?? 'active') === 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi {{ ($service->status ?? 'active') === 'active' ? 'bi-pause' : 'bi-play' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noServicesRow">
                        <td colspan="8" class="text-center py-4 text-muted">No services found. Click "Add Service" to create one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Alert container for inline notifications -->
<div id="serviceAlertContainer" style="position:fixed;top:20px;right:20px;z-index:99999;"></div>

<!-- Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="serviceForm" class="modal-content">
            @csrf
            <input type="hidden" id="serviceId" name="serviceId" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="serviceFormErrors" class="alert alert-danger d-none"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <input type="text" class="form-control" id="short_description" name="short_description" maxlength="500">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price (TSh)</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Duration (hours)</label>
                        <input type="number" class="form-control" id="duration_hours" name="duration_hours" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                        <div id="featuredImagePreview" class="mt-2"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Featured Service</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                            <label class="form-check-label" for="is_featured">
                                Mark as featured service
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sa-primary" id="saveServiceBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <span id="saveBtnText">Save Service</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Service Modal -->
<div class="modal fade" id="viewServiceModal" tabindex="-1" aria-labelledby="viewServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewServiceModalLabel">Service Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="serviceDetailsBody">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmMessage">Are you sure you want to delete this service? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // ---------- DOM references with null safety ----------
    const $ = (id) => {
        const el = document.getElementById(id);
        if (!el) console.warn('Element #' + id + ' not found');
        return el;
    };

    const serviceModalEl = document.getElementById('serviceModal');
    const viewModalEl = document.getElementById('viewServiceModal');
    const deleteModalEl = document.getElementById('deleteConfirmModal');

    if (!serviceModalEl || !viewModalEl || !deleteModalEl) {
        console.error('Service Management: One or more modal elements missing.');
        return;
    }

    const serviceModal = new bootstrap.Modal(serviceModalEl);
    const viewModal = new bootstrap.Modal(viewModalEl);
    const deleteModal = new bootstrap.Modal(deleteModalEl);

    const tbody = document.getElementById('servicesTableBody');
    const alertContainer = document.getElementById('serviceAlertContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let currentServiceId = null;
    let isSubmitting = false;

    // ---------- Utility functions ----------
    function safeVal(id, val) {
        const el = $(id);
        if (el) el.value = val ?? '';
    }

    function safeHtml(id, html) {
        const el = $(id);
        if (el) el.innerHTML = html ?? '';
    }

    function safeChecked(id, checked) {
        const el = $(id);
        if (el) el.checked = !!checked;
    }

    function getVal(id) {
        const el = $(id);
        return el ? el.value : '';
    }

    function getChecked(id) {
        const el = $(id);
        return el ? el.checked : false;
    }

    function getFiles(id) {
        const el = $(id);
        return el ? el.files : null;
    }

    function setBtnLoading(btn, loading) {
        if (!btn) return;
        const spinner = btn.querySelector('.spinner-border');
        const text = btn.querySelector('span:not(.spinner-border)');
        btn.disabled = loading;
        if (spinner) spinner.classList.toggle('d-none', !loading);
        if (text && loading) text.textContent = 'Saving...';
        else if (text && !loading) text.textContent = 'Save Service';
    }

    function setDelBtnLoading(loading) {
        const btn = document.getElementById('confirmDeleteBtn');
        if (!btn) return;
        const spinner = btn.querySelector('.spinner-border');
        btn.disabled = loading;
        if (spinner) spinner.classList.toggle('d-none', !loading);
        btn.textContent = loading ? 'Deleting...' : 'Delete';
        if (loading && spinner) {
            btn.innerHTML = '';
            btn.appendChild(spinner);
            btn.appendChild(document.createTextNode(' Deleting...'));
        }
    }

    // ---------- Notifications ----------
    function showNotification(message, type) {
        if (!alertContainer) return;
        const div = document.createElement('div');
        div.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        div.style.minWidth = '300px';
        div.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        alertContainer.appendChild(div);
        setTimeout(function() {
            if (div.parentNode) div.remove();
        }, 4000);
    }

    function showFormErrors(errors) {
        const container = document.getElementById('serviceFormErrors');
        if (!container) return;
        if (!errors || errors.length === 0) {
            container.classList.add('d-none');
            return;
        }
        container.classList.remove('d-none');
        container.innerHTML = '<strong>Validation Errors:</strong><ul class="mb-0 mt-1">' + 
            errors.map(function(e) { return '<li>' + e + '</li>'; }).join('') + '</ul>';
    }

    function hideFormErrors() {
        const container = document.getElementById('serviceFormErrors');
        if (container) container.classList.add('d-none');
    }

    // ---------- Form Reset ----------
    function resetServiceForm() {
        const form = document.getElementById('serviceForm');
        if (form) form.reset();
        safeVal('serviceId', '');
        safeHtml('featuredImagePreview', '');
        hideFormErrors();
        setBtnLoading(document.getElementById('saveServiceBtn'), false);
    }

    // ---------- Add Service ----------
    function setupAddService() {
        const btn = document.getElementById('addServiceBtn');
        if (!btn) return;
        btn.addEventListener('click', function() {
            safeHtml('serviceModalLabel', 'Add New Service');
            resetServiceForm();
            serviceModal.show();
        });
    }

    // ---------- Event delegation for table actions ----------
    function setupTableActions() {
        if (!tbody) return;
        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('button');
            if (!btn || !btn.dataset.id) return;
            const id = btn.dataset.id;

            if (btn.classList.contains('edit-service')) {
                loadServiceForEdit(id);
            } else if (btn.classList.contains('view-service')) {
                viewService(id);
            } else if (btn.classList.contains('delete-service')) {
                currentServiceId = id;
                const msgEl = document.getElementById('deleteConfirmMessage');
                if (msgEl) msgEl.textContent = 'Are you sure you want to delete this service? This action cannot be undone.';
                deleteModal.show();
            } else if (btn.classList.contains('toggle-status')) {
                toggleServiceStatus(id);
            }
        });
    }

    // ---------- Load Service for Edit ----------
    function loadServiceForEdit(id) {
        console.log('Loading service for edit:', id);
        safeHtml('serviceModalLabel', 'Edit Service');
        resetServiceForm();

        fetch('/super-admin/services/' + id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                console.log('Edit service response:', data);
                if (data.success) {
                    var s = data.service;
                    safeVal('serviceId', s.id);
                    safeVal('name', s.name);
                    safeVal('category_id', s.category_id || '');
                    safeVal('short_description', s.short_description || '');
                    safeVal('description', s.description || '');
                    safeVal('price', s.base_price || '');
                    safeVal('duration_hours', s.duration_hours || '');
                    safeVal('status', s.status || 'active');
                    safeChecked('is_featured', s.is_featured);
                    safeHtml('featuredImagePreview', s.featured_image 
                        ? '<img src="/storage/' + s.featured_image + '" class="img-thumbnail" style="max-height:100px;">' 
                        : '');
                    serviceModal.show();
                } else {
                    showNotification(data.message || 'Failed to load service', 'error');
                }
            })
            .catch(function(err) {
                console.error('Error loading service for edit:', err);
                showNotification('Failed to load service details', 'error');
            });
    }

    // ---------- View Service ----------
    function viewService(id) {
        console.log('Viewing service:', id);
        safeHtml('serviceDetailsBody', '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading...</p></div>');
        viewModal.show();

        fetch('/super-admin/services/' + id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                console.log('View service response:', data);
                if (data.success) {
                    var s = data.service;
                    var imgUrl = s.featured_image_url || 'https://placehold.co/600x400/e2e8f0/64748b?text=Service';
                    var category = s.category ? s.category.name : '-';
                    var price = s.base_price ? Number(s.base_price).toLocaleString() : '0';
                    var duration = s.duration_hours || '-';
                    var statusBadge = s.status === 'active' ? 'bg-success' : 'bg-secondary';
                    var featuredBadge = s.is_featured ? '<span class="badge bg-warning text-dark">Featured</span>' : '-';

                    safeHtml('serviceDetailsBody', 
                        '<div class="row">' +
                            '<div class="col-md-4 text-center">' +
                                '<img src="' + imgUrl + '" alt="' + s.name + '" class="img-fluid rounded mb-3" onerror="this.src=\'https://placehold.co/600x400/e2e8f0/64748b?text=Service\'">' +
                            '</div>' +
                            '<div class="col-md-8">' +
                                '<h4>' + s.name + '</h4>' +
                                '<p><strong>Category:</strong> ' + category + '</p>' +
                                '<p><strong>Price:</strong> TSh ' + price + '</p>' +
                                '<p><strong>Duration:</strong> ' + duration + ' hours</p>' +
                                '<p><strong>Status:</strong> <span class="badge ' + statusBadge + '">' + (s.status || 'active') + '</span></p>' +
                                '<p><strong>Featured:</strong> ' + featuredBadge + '</p>' +
                                '<hr>' +
                                '<p>' + (s.short_description || '') + '</p>' +
                                '<p>' + (s.description || '') + '</p>' +
                            '</div>' +
                        '</div>'
                    );
                } else {
                    safeHtml('serviceDetailsBody', '<p class="text-danger text-center py-4">Failed to load service details.</p>');
                }
            })
            .catch(function(err) {
                console.error('Error viewing service:', err);
                safeHtml('serviceDetailsBody', '<p class="text-danger text-center py-4">An error occurred while loading details.</p>');
            });
    }

    // ---------- Save Service (Create/Update) ----------
    function saveService() {
        if (isSubmitting) {
            console.log('Save already in progress, skipping...');
            return;
        }

        var id = getVal('serviceId');
        console.log('Saving service. ID:', id || '(new)');
        hideFormErrors();

        var formData = new FormData();
        formData.append('name', getVal('name'));
        formData.append('category_id', getVal('category_id'));
        formData.append('short_description', getVal('short_description'));
        formData.append('description', getVal('description'));
        formData.append('price', getVal('price'));
        formData.append('duration_hours', getVal('duration_hours'));
        formData.append('status', getVal('status'));
        formData.append('is_featured', getChecked('is_featured') ? '1' : '0');

        var imageFile = getFiles('featured_image');
        if (imageFile && imageFile.length > 0) {
            formData.append('featured_image', imageFile[0]);
        }

        var url = id ? '/super-admin/services/' + id : '/super-admin/services';
        var method = id ? 'PUT' : 'POST';
        formData.append('_method', method);

        var btn = document.getElementById('saveServiceBtn');
        setBtnLoading(btn, true);
        isSubmitting = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(response) {
            return response.json().then(function(data) {
                return { status: response.status, ok: response.ok, data: data };
            });
        })
        .then(function(result) {
            console.log('Save response status:', result.status, 'data:', result.data);
            setBtnLoading(btn, false);
            isSubmitting = false;

            if (result.ok && result.data.success) {
                serviceModal.hide();
                showNotification(result.data.message || 'Service saved successfully.', 'success');
                resetServiceForm();
                setTimeout(function() { location.reload(); }, 800);
            } else if (result.status === 422 && result.data.errors) {
                // Validation errors
                var errorMessages = [];
                for (var field in result.data.errors) {
                    if (result.data.errors.hasOwnProperty(field)) {
                        errorMessages = errorMessages.concat(result.data.errors[field]);
                    }
                }
                showFormErrors(errorMessages);
                showNotification('Please check the form for errors.', 'error');
            } else {
                showNotification(result.data.message || 'Error saving service. Please try again.', 'error');
            }
        })
        .catch(function(err) {
            console.error('Save service error:', err);
            setBtnLoading(btn, false);
            isSubmitting = false;
            showNotification('Network error. Please check your connection and try again.', 'error');
        });
    }

    // ---------- Delete Service ----------
    function deleteService() {
        if (!currentServiceId) return;
        console.log('Deleting service:', currentServiceId);
        setDelBtnLoading(true);

        fetch('/super-admin/services/' + currentServiceId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            console.log('Delete response:', data);
            setDelBtnLoading(false);
            deleteModal.hide();
            if (data.success) {
                showNotification(data.message || 'Service deleted successfully.', 'success');
                var row = document.querySelector('tr[data-id="' + currentServiceId + '"]');
                if (row) row.remove();
                currentServiceId = null;
            } else {
                showNotification(data.message || 'Error deleting service.', 'error');
            }
        })
        .catch(function(err) {
            console.error('Delete error:', err);
            setDelBtnLoading(false);
            deleteModal.hide();
            showNotification('Network error. Please try again.', 'error');
        });
    }

    // ---------- Toggle Service Status ----------
    function toggleServiceStatus(id) {
        console.log('Toggling status for:', id);
        fetch('/super-admin/services/' + id + '/toggle-status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            console.log('Toggle status response:', data);
            if (data.success) {
                showNotification(data.message || 'Status updated.', 'success');
                var row = document.querySelector('tr[data-id="' + id + '"]');
                if (row) {
                    var badge = row.querySelector('.sa-badge');
                    if (badge) {
                        badge.textContent = data.status;
                        badge.className = 'sa-badge ' + (data.status === 'active' ? 'sa-badge-active' : 'sa-badge-inactive');
                    }
                }
            } else {
                showNotification(data.message || 'Error updating status.', 'error');
            }
        })
        .catch(function(err) {
            console.error('Toggle status error:', err);
            showNotification('Network error.', 'error');
        });
    }

    // ---------- Image Preview ----------
    function setupImagePreview() {
        var input = document.getElementById('featured_image');
        if (!input) return;
        input.addEventListener('change', function(e) {
            var file = e.target.files ? e.target.files[0] : null;
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(ev) {
                var preview = document.getElementById('featuredImagePreview');
                if (preview) {
                    preview.innerHTML = '<img src="' + ev.target.result + '" class="img-thumbnail" style="max-height:100px;">';
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // ---------- Initialize ----------
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Service Management JS initializing...');

        // Add Service button
        setupAddService();

        // Table action buttons via event delegation
        setupTableActions();

        // Form submit
        var form = document.getElementById('serviceForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');
                saveService();
            });
        } else {
            console.error('Service form not found');
        }

        // Delete confirmation
        var confirmDelBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDelBtn) {
            confirmDelBtn.addEventListener('click', function() {
                deleteService();
            });
        }

        // Image preview
        setupImagePreview();

        // Clean up modal backdrop when hidden
        serviceModalEl.addEventListener('hidden.bs.modal', function() {
            resetServiceForm();
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(b) { b.remove(); });
        });

        console.log('Service Management JS initialized successfully.');
    });
})();
</script>
@endpush