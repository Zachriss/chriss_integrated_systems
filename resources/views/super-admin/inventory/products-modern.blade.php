@extends('super-admin.layouts.super-admin')

@section('title', 'Product Management')

@push('styles')
<style>
    .inventory-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
    }
    .stat-card.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.danger { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .stat-card.success { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-value { font-size: 2rem; font-weight: 800; }
    .stat-label { font-size: 0.85rem; opacity: 0.9; }

    .product-image-preview {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        background: #f3f4f6;
    }
    .stock-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    .stock-low { background: #fef3c7; color: #92400e; }
    .stock-out { background: #fee2e2; color: #991b1b; }
    .stock-ok { background: #dcfce7; color: #166534; }

    .filter-section {
        background: #f8fafc;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .modal-lg-custom { max-width: 900px; }
    .image-preview-container {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
    .image-preview-item {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
    }
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .remove-image {
        position: absolute;
        top: 2px;
        right: 2px;
        background: #ef4444;
        color: white;
        border: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Product Management</h1>
        <p>Manage inventory products with e-commerce style interface.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sa-primary" onclick="openProductModal()">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </button>
        <button class="btn btn-outline-secondary" onclick="openCategoryModal()">
            <i class="bi bi-tags me-1"></i> Manage Categories
        </button>
    </div>
</div>

<div class="inventory-stats" id="stats-container">
    <div class="stat-card">
        <div class="stat-value" id="total-products">-</div>
        <div class="stat-label">Total Products</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-value" id="low-stock">-</div>
        <div class="stat-label">Low Stock Items</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-value" id="out-of-stock">-</div>
        <div class="stat-label">Out of Stock</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value" id="featured-count">-</div>
        <div class="stat-label">Featured Products</div>
    </div>
</div>

<div class="filter-section">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small">Search</label>
            <input type="text" class="form-control" id="search-input" placeholder="Search products...">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Category</label>
            <select class="form-select" id="category-filter">
                <option value="">All Categories</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Status</label>
            <select class="form-select" id="status-filter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Stock</label>
            <select class="form-select" id="stock-filter">
                <option value="">All Stock</option>
                <option value="in-stock">In Stock</option>
                <option value="low-stock">Low Stock</option>
                <option value="out-of-stock">Out of Stock</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </button>
        </div>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table" id="products-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="products-tbody">
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Loading products...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3" id="pagination-container">
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <form id="product-form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="product-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU *</label>
                            <input type="text" class="form-control" name="sku" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" id="product-category">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <input type="text" class="form-control" name="short_description">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Buying Price *</label>
                            <input type="number" class="form-control" name="buying_price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selling Price *</label>
                            <input type="number" class="form-control" name="selling_price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="quantity" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Stock Alert</label>
                            <input type="number" class="form-control" name="low_stock_alert_level" min="0" value="5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <input type="text" class="form-control" name="barcode">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Main Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <div id="main-image-preview" class="image-preview-container"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gallery Images (Multiple)</label>
                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                            <div id="gallery-preview" class="image-preview-container"></div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is-featured">
                                <label class="form-check-label" for="is-featured">Feature this product</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sa-primary">
                        <span class="spinner-border spinner-border-sm d-none" id="submit-spinner"></span>
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="restock-form">
            <input type="hidden" name="product_id" id="restock-product-id">
            <div class="modal-header">
                <h5 class="modal-title">Restock Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <div class="fw-bold" id="restock-product-name"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Current Stock</label>
                    <div id="restock-current-stock" class="text-muted"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Add Quantity *</label>
                    <input type="number" class="form-control" name="quantity" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Restock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let categories = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadProducts();
    loadStats();

    document.getElementById('product-form').addEventListener('submit', handleProductSubmit);
    document.getElementById('restock-form').addEventListener('submit', handleRestockSubmit);
    document.getElementById('search-input').addEventListener('input', debounce(loadProducts, 300));
    document.getElementById('category-filter').addEventListener('change', loadProducts);
    document.getElementById('status-filter').addEventListener('change', loadProducts);
    document.getElementById('stock-filter').addEventListener('change', loadProducts);
});

function loadCategories() {
    fetch('{{ route('super-admin.inventory.categories.data') }}')
        .then(r => r.json())
        .then(data => {
            categories = data.data || [];
            const select = document.getElementById('category-filter');
            const productSelect = document.getElementById('product-category');
            categories.forEach(cat => {
                select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                productSelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        });
}

function loadProducts(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('products-tbody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading...</td></tr>';

    const params = new URLSearchParams({
        page: page,
        category_id: document.getElementById('category-filter').value,
        status: document.getElementById('status-filter').value,
        search: document.getElementById('search-input').value,
    });

    fetch(`{{ route('super-admin.inventory.products.data') }}?${params}`)
        .then(r => r.json())
        .then(data => {
            renderProducts(data.data || []);
            renderPagination(data.meta || {});
        });
}

function renderProducts(products) {
    const tbody = document.getElementById('products-tbody');
    if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No products found.</td></tr>';
        return;
    }

    tbody.innerHTML = products.map(p => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <img src="${p.image_url}" class="product-image-preview me-2" alt="${p.name}">
                    <div>
                        <div class="fw-semibold">${p.name}</div>
                        <small class="text-muted">${p.short_description || ''}</small>
                    </div>
                </div>
            </td>
            <td>${p.category?.name || '—'}</td>
            <td><code>${p.sku}</code></td>
            <td class="fw-bold">TSh ${Number(p.selling_price).toLocaleString()}</td>
            <td>
                <span class="badge stock-badge ${getStockClass(p)}">${p.quantity} units</span>
            </td>
            <td>
                <span class="badge ${p.status === 'active' ? 'sa-badge-active' : 'sa-badge-inactive'}">
                    ${p.status}
                </span>
            </td>
            <td>
                ${p.is_featured ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>'}
            </td>
            <td>${p.creator?.name || '—'}</td>
            <td>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-sa-outline btn-outline-primary" onclick="openProductModal(${p.id})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-sa-outline btn-outline-success" onclick="openRestockModal(${p.id}, '${p.name}', ${p.quantity})" title="Restock">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-sa-outline ${p.status === 'active' ? 'btn-outline-warning' : 'btn-outline-info'}" onclick="toggleProductStatus(${p.id})" title="${p.status === 'active' ? 'Deactivate' : 'Activate'}">
                        <i class="bi bi-${p.status === 'active' ? 'pause' : 'play'}"></i>
                    </button>
                    <button class="btn btn-sm btn-sa-outline btn-outline-danger" onclick="deleteProduct(${p.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function getStockClass(p) {
    if (p.quantity <= 0) return 'stock-out';
    if (p.quantity <= p.low_stock_alert_level) return 'stock-low';
    return 'stock-ok';
}

function renderPagination(meta) {
    const container = document.getElementById('pagination-container');
    if (!meta.last_page || meta.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination pagination-sm">';
    for (let i = 1; i <= meta.last_page; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadProducts(${i}); return false;">${i}</a></li>`;
    }
    html += '</ul></nav>';
    container.innerHTML = html;
}

function loadStats() {
    fetch('{{ route('super-admin.inventory.products.data') }}')
        .then(r => r.json())
        .then(data => {
            const products = data.data || [];
            document.getElementById('total-products').textContent = products.length;
            document.getElementById('low-stock').textContent = products.filter(p => p.quantity > 0 && p.quantity <= p.low_stock_alert_level).length;
            document.getElementById('out-of-stock').textContent = products.filter(p => p.quantity <= 0).length;
            document.getElementById('featured-count').textContent = products.filter(p => p.is_featured).length;
        });
}

function openProductModal(productId = null) {
    const form = document.getElementById('product-form');
    form.reset();
    document.getElementById('product-id').value = '';
    document.getElementById('main-image-preview').innerHTML = '';
    document.getElementById('gallery-preview').innerHTML = '';

    if (productId) {
        document.querySelector('.modal-title').textContent = 'Edit Product';
        fetch(`{{ route('super-admin.inventory.products.show', ['product' => ':id']) }}`.replace(':id', productId))
            .then(r => r.json())
            .then(data => {
                const p = data.product;
                document.getElementById('product-id').value = p.id;
                Object.keys(p).forEach(key => {
                    const el = form.querySelector(`[name="${key}"]`);
                    if (el) el.value = p[key];
                });
                if (p.image_url) {
                    document.getElementById('main-image-preview').innerHTML = `<img src="${p.image_url}" style="max-height:80px;border-radius:8px;">`;
                }
            });
    } else {
        document.querySelector('.modal-title').textContent = 'Add New Product';
    }

    new bootstrap.Modal(document.getElementById('productModal')).show();
}

function handleProductSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const spinner = document.getElementById('submit-spinner');
    spinner.classList.remove('d-none');

    const formData = new FormData(form);
    const productId = document.getElementById('product-id').value;
    const method = productId ? 'PUT' : 'POST';
    const url = productId
        ? `{{ route('super-admin.inventory.products.update', ['product' => ':id']) }}`.replace(':id', productId)
        : '{{ route('super-admin.inventory.products.store') }}';

    fetch(url, {
        method: method,
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        spinner.classList.add('d-none');
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            showAlert('success', data.message);
            loadProducts(currentPage);
            loadStats();
        } else {
            showAlert('error', data.message || 'Validation error');
        }
    });
}

function openRestockModal(id, name, stock) {
    document.getElementById('restock-product-id').value = id;
    document.getElementById('restock-product-name').textContent = name;
    document.getElementById('restock-current-stock').textContent = stock + ' units';
    document.getElementById('restock-form').reset();
    new bootstrap.Modal(document.getElementById('restockModal')).show();
}

function handleRestockSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const productId = document.getElementById('restock-product-id').value;

    fetch(`{{ route('super-admin.inventory.products.restock', ['product' => ':id']) }}`.replace(':id', productId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(new FormData(form)))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('restockModal')).hide();
            showAlert('success', data.message);
            loadProducts(currentPage);
            loadStats();
        } else {
            showAlert('error', data.message);
        }
    });
}

function toggleProductStatus(id) {
    fetch(`{{ route('super-admin.inventory.products.toggle-status', ['product' => ':id']) }}`.replace(':id', id), {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadProducts(currentPage);
        }
    });
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This product will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ route('super-admin.inventory.products.destroy', ['product' => ':id']) }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    loadProducts(currentPage);
                    loadStats();
                }
            });
        }
    });
}

function resetFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('stock-filter').value = '';
    loadProducts();
}

function showAlert(type, message) {
    if (typeof showSystemAlert === 'function') {
        showSystemAlert({theme: type, title: type === 'success' ? 'Success' : 'Error', text: message, timer: 2500, showConfirmButton: false});
    } else {
        alert(message);
    }
}

function debounce(fn, delay) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(context, args), delay);
    };
}
</script>
@endpush