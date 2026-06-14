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
    }
    .category-card:hover { transform: translateY(-2px); }
    .category-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin: 0 auto 0.5rem;
        background: #f3f4f6;
    }
    .category-count { font-size: 0.8rem; color: #64748b; }
</style>
@endpush

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Product Categories</h1>
        <p>Manage product categories for inventory organization.</p>
    </div>
    <button class="btn btn-sa-primary" onclick="openCategoryModal()">
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

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="category-form" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="category_id" id="category-id">
                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="category-active" checked>
                        <label class="form-check-label" for="category-active">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sa-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    document.getElementById('category-form').addEventListener('submit', handleCategorySubmit);
});

function loadCategories() {
    fetch('{{ route('super-admin.inventory.categories.data') }}')
        .then(r => r.json())
        .then(data => {
            renderCategories(data.data || []);
        });
}

function renderCategories(categories) {
    const grid = document.getElementById('categories-grid');
    if (!categories.length) {
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">No categories found.</div>';
        return;
    }

    grid.innerHTML = categories.map(cat => `
        <div class="col-md-3 col-sm-6">
            <div class="category-card h-100">
                ${cat.image ? `<img src="/storage/${cat.image}" class="category-image" alt="${cat.name}">` : '<div class="category-image d-flex align-items-center justify-content-center"><i class="bi bi-tags text-muted"></i></div>'}
                <h6 class="mb-1">${cat.name}</h6>
                <div class="category-count">${cat.products_count} products</div>
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="openCategoryModal(${cat.id})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${cat.id})"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
    `).join('');
}

function openCategoryModal(id = null) {
    const form = document.getElementById('category-form');
    form.reset();
    document.getElementById('category-id').value = '';

    if (id) {
        document.querySelector('#categoryModal .modal-title').textContent = 'Edit Category';
        fetch(`{{ route('super-admin.inventory.categories.show', ['category' => ':id']) }}`.replace(':id', id))
            .then(r => r.json())
            .then(data => {
                Object.keys(data.category).forEach(key => {
                    const el = form.querySelector(`[name="${key}"]`);
                    if (el) el.value = data.category[key];
                });
            });
    } else {
        document.querySelector('#categoryModal .modal-title').textContent = 'Add Category';
    }

    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function handleCategorySubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const id = document.getElementById('category-id').value;
    const url = id ? `{{ route('super-admin.inventory.categories.update', ['category' => ':id']) }}`.replace(':id', id) : '{{ route('super-admin.inventory.categories.store') }}';
    const method = id ? 'POST' : 'POST';

    fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: (() => { if (id) formData.append('_method', 'PUT'); return formData; })()
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            showAlert('success', data.message);
            loadCategories();
            loadProductCategories();
        } else {
            showAlert('error', data.message);
        }
    });
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Delete this category?',
        text: 'Products in this category will need to be reassigned.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ route('super-admin.inventory.categories.destroy', ['category' => ':id']) }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    loadCategories();
                } else {
                    showAlert('error', data.message);
                }
            });
        }
    });
}

function showAlert(type, message) {
    if (typeof showSystemAlert === 'function') {
        showSystemAlert({theme: type, title: type === 'success' ? 'Success' : 'Error', text: message, timer: 2500, showConfirmButton: false});
    }
}
</script>
@endpush