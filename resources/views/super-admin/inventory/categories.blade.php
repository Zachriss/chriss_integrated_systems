@extends('super-admin.layouts.super-admin')

@section('title', 'Product Categories')

@push('styles')
<style>
    .category-card {
        background: white;
        border: 1px solid #e0e4eb;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        transition: transform 0.2s;
        height: 100%;
    }
    .category-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .category-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 0.5rem;
        background: #e8f1ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 1.3rem;
    }
    .category-count { font-size: 0.8rem; color: #64748b; }
    .category-actions { margin-top: 0.75rem; }
</style>
@endpush

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Product Categories</h1>
        <p>Manage product categories for inventory organization.</p>
    </div>
    <button class="btn btn-sa-primary" id="addCatBtn">
        <i class="bi bi-plus-lg me-1"></i> Add Category
    </button>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <div id="categories-grid" class="row g-3">
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary"></div>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="category-form">
            <input type="hidden" name="category_id" id="category-id">
            <div class="modal-header">
                <h5 class="modal-title" id="catModalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="catFormAlert" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" class="form-control" name="name" id="cat_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="cat_desc" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="cat_active" checked>
                        <label class="form-check-label" for="cat_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sa-primary" id="saveCatBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <span id="saveCatBtnText">Save</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation -->
<div class="modal fade" id="deleteCatConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category?</p>
                <p class="text-muted small mb-0">Products in this category will need to be reassigned.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelCatBtn">
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

    // DOM refs
    var grid = document.getElementById('categories-grid');
    var catModalEl = document.getElementById('categoryModal');
    var delModalEl = document.getElementById('deleteCatConfirmModal');
    var catForm = document.getElementById('category-form');
    var catIdInput = document.getElementById('category-id');
    var catNameInput = document.getElementById('cat_name');
    var catDescInput = document.getElementById('cat_desc');
    var catActiveCheck = document.getElementById('cat_active');
    var saveBtn = document.getElementById('saveCatBtn');
    var saveBtnText = document.getElementById('saveCatBtnText');
    var confirmDelBtn = document.getElementById('confirmDelCatBtn');
    var catFormAlert = document.getElementById('catFormAlert');
    var CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (!catModalEl || !delModalEl) { console.error('Modal elements missing'); return; }

    var catModal = new bootstrap.Modal(catModalEl);
    var delModal = new bootstrap.Modal(delModalEl);
    var isSubmitting = false;
    var deleteId = null;

    // ---------- helpers ----------
    function showAlert(msg, type) {
        if (!catFormAlert) return;
        catFormAlert.classList.remove('d-none');
        catFormAlert.className = 'alert alert-' + (type === 'error' ? 'danger' : type) + ' d-none';
        catFormAlert.classList.remove('d-none');
        catFormAlert.textContent = msg;
    }

    function hideAlert() {
        if (catFormAlert) catFormAlert.classList.add('d-none');
    }

    function resetForm() {
        if (catForm) catForm.reset();
        if (catIdInput) catIdInput.value = '';
        if (catActiveCheck) catActiveCheck.checked = true;
        hideAlert();
        if (saveBtn) { saveBtn.disabled = false; }
        if (saveBtnText) saveBtnText.textContent = 'Save';
        var sp = saveBtn ? saveBtn.querySelector('.spinner-border') : null;
        if (sp) sp.classList.add('d-none');
    }

    function setBtnLoading(loading) {
        if (!saveBtn) return;
        saveBtn.disabled = loading;
        var sp = saveBtn.querySelector('.spinner-border');
        if (sp) sp.classList.toggle('d-none', !loading);
        if (saveBtnText) saveBtnText.textContent = loading ? 'Saving...' : 'Save';
    }

    function showNotif(msg, type) {
        var c = document.getElementById('invCatAlert');
        if (!c) {
            c = document.createElement('div');
            c.id = 'invCatAlert';
            c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;';
            document.body.appendChild(c);
        }
        var d = document.createElement('div');
        d.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        d.style.minWidth = '280px';
        d.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        c.appendChild(d);
        setTimeout(function() { if (d.parentNode) d.remove(); }, 4000);
    }

    // ---------- load categories ----------
    function loadCategories() {
        if (!grid) return;
        grid.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary"></div></div>';
        fetch('{{ route('super-admin.inventory.categories.data') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderCategories(data.data || []);
            })
            .catch(function() {
                grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">Failed to load categories.</div>';
            });
    }

    function renderCategories(cats) {
        if (!grid) return;
        if (!cats.length) {
            grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">No categories found.</div>';
            return;
        }
        grid.innerHTML = cats.map(function(cat) {
            return '<div class="col-md-3 col-sm-6">' +
                '<div class="category-card">' +
                    '<div class="category-icon"><i class="bi bi-tags"></i></div>' +
                    '<h6 class="mb-1">' + cat.name + '</h6>' +
                    '<div class="category-count">' + (cat.products_count || 0) + ' products</div>' +
                    '<div class="category-actions">' +
                        '<button class="btn btn-sm btn-outline-primary edit-cat-btn" data-id="' + cat.id + '"><i class="bi bi-pencil"></i></button> ' +
                        '<button class="btn btn-sm btn-outline-danger del-cat-btn" data-id="' + cat.id + '"><i class="bi bi-trash"></i></button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');
    }

    // ---------- grid event delegation ----------
    if (grid) {
        grid.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn || !btn.dataset.id) return;
            var id = btn.dataset.id;
            if (btn.classList.contains('edit-cat-btn')) {
                openCategoryModal(id);
            } else if (btn.classList.contains('del-cat-btn')) {
                deleteId = id;
                delModal.show();
            }
        });
    }

    // ---------- add button ----------
    var addBtn = document.getElementById('addCatBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            openCategoryModal(null);
        });
    }

    // ---------- open modal ----------
    function openCategoryModal(id) {
        var titleEl = document.getElementById('catModalTitle');
        resetForm();

        if (!id) {
            if (titleEl) titleEl.textContent = 'Add Category';
            catModal.show();
            return;
        }

        if (titleEl) titleEl.textContent = 'Edit Category';

        fetch('{{ route('super-admin.inventory.categories.show', ['category' => ':id']) }}'.replace(':id', id))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var cat = data.category || data;
                if (catIdInput) catIdInput.value = cat.id || '';
                if (catNameInput) catNameInput.value = cat.name || '';
                if (catDescInput) catDescInput.value = cat.description || '';
                if (catActiveCheck) catActiveCheck.checked = cat.is_active !== false;
                catModal.show();
            })
            .catch(function() {
                showNotif('Failed to load category', 'error');
            });
    }

    // ---------- form submit ----------
    if (catForm) {
        catForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Product category form submit');

            if (isSubmitting) return;
            hideAlert();

            var name = catNameInput ? catNameInput.value.trim() : '';
            if (!name) {
                showAlert('Category name is required.', 'error');
                return;
            }

            var id = catIdInput ? catIdInput.value : '';
            var formData = new FormData();
            formData.append('name', name);
            formData.append('description', catDescInput ? catDescInput.value : '');
            formData.append('is_active', catActiveCheck && catActiveCheck.checked ? '1' : '0');

            var url = id ? '/super-admin/inventory/categories/' + id : '/super-admin/inventory/categories';
            var method = id ? 'PUT' : 'POST';
            formData.append('_method', method);

            setBtnLoading(true);
            isSubmitting = true;

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { status: response.status, ok: response.ok, data: data };
                });
            })
            .then(function(result) {
                console.log('Save response:', result);
                setBtnLoading(false);
                isSubmitting = false;

                if (result.ok && result.data.success) {
                    catModal.hide();
                    showNotif(result.data.message, 'success');
                    resetForm();
                    loadCategories();
                } else if (result.status === 422 && result.data.errors) {
                    var msgs = [];
                    for (var f in result.data.errors) {
                        if (result.data.errors.hasOwnProperty(f)) msgs = msgs.concat(result.data.errors[f]);
                    }
                    showAlert(msgs.join(' | '), 'error');
                } else {
                    showAlert(result.data.message || 'Error saving category', 'error');
                }
            })
            .catch(function(err) {
                console.error('Save error:', err);
                setBtnLoading(false);
                isSubmitting = false;
                showAlert('Network error. Please try again.', 'error');
            });
        });
    }

    // ---------- delete confirmation ----------
    if (confirmDelBtn) {
        confirmDelBtn.addEventListener('click', function() {
            if (!deleteId) return;
            confirmDelBtn.disabled = true;
            var sp = confirmDelBtn.querySelector('.spinner-border');
            if (sp) sp.classList.remove('d-none');
            confirmDelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';

            fetch('/super-admin/inventory/categories/' + deleteId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                confirmDelBtn.disabled = false;
                confirmDelBtn.textContent = 'Delete';
                if (sp) sp.classList.add('d-none');
                delModal.hide();

                if (data.success) {
                    showNotif(data.message, 'success');
                    loadCategories();
                } else {
                    showNotif(data.message || 'Error deleting', 'error');
                }
                deleteId = null;
            })
            .catch(function(err) {
                console.error('Delete error:', err);
                confirmDelBtn.disabled = false;
                confirmDelBtn.textContent = 'Delete';
                if (sp) sp.classList.add('d-none');
                delModal.hide();
                showNotif('Network error', 'error');
            });
        });
    }

    // ---------- modal cleanup ----------
    if (catModalEl) {
        catModalEl.addEventListener('hidden.bs.modal', function() {
            resetForm();
            document.body.classList.remove('modal-open');
            var bd = document.querySelectorAll('.modal-backdrop');
            bd.forEach(function(b) { b.remove(); });
        });
    }

    if (delModalEl) {
        delModalEl.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open');
            var bd = document.querySelectorAll('.modal-backdrop');
            bd.forEach(function(b) { b.remove(); });
        });
    }

    // ---------- init ----------
    loadCategories();
    console.log('Product Categories JS initialized.');
})();
</script>
@endpush