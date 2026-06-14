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
    .product-image-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        color: #64748b;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
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
        <button class="btn btn-sa-primary" id="addProductBtn">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </button>
        <button class="btn btn-outline-secondary" id="manageCatBtn">
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
            <button class="btn btn-outline-secondary w-100" id="resetFiltersBtn">
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

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <form id="product-form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="productFormAlert" class="alert alert-danger d-none"></div>
                    <input type="hidden" name="product_id" id="product-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" id="p_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU *</label>
                            <input type="text" class="form-control" name="sku" id="p_sku" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" id="product-category">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand" id="p_brand">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <input type="text" class="form-control" name="short_description" id="p_short_desc">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control" name="description" id="p_desc" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Buying Price *</label>
                            <input type="number" class="form-control" name="buying_price" id="p_buy_price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selling Price *</label>
                            <input type="number" class="form-control" name="selling_price" id="p_sell_price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="quantity" id="p_qty" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Stock Alert</label>
                            <input type="number" class="form-control" name="low_stock_alert_level" id="p_low_stock" min="0" value="5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <input type="text" class="form-control" name="barcode" id="p_barcode">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" id="p_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Main Image</label>
                            <input type="file" class="form-control" name="image" id="p_image" accept="image/*">
                            <div id="main-image-preview" class="image-preview-container"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gallery Images (Multiple)</label>
                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                            <div id="gallery-preview" class="image-preview-container"></div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="p_featured">
                                <label class="form-check-label" for="is_featured">Feature this product</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sa-primary" id="saveProductBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="submit-spinner" role="status"></span>
                        <span id="saveProductBtnText">Save Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restock Modal -->
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
                <button type="submit" class="btn btn-success" id="restockBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                    <span>Restock</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelProductBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal (Simple) -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="category-form">
            <input type="hidden" name="category_id" id="cat-id">
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
                <button type="submit" class="btn btn-sa-primary" id="saveProdcutCatBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                    <span>Save</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // DOM refs
    var tbody = document.getElementById('products-tbody');
    var productModalEl = document.getElementById('productModal');
    var restockModalEl = document.getElementById('restockModal');
    var deleteModalEl = document.getElementById('deleteProductModal');
    var categoryModalEl = document.getElementById('categoryModal');
    var productForm = document.getElementById('product-form');
    var restockForm = document.getElementById('restock-form');
    var categoryForm = document.getElementById('category-form');
    var searchInput = document.getElementById('search-input');
    var catFilter = document.getElementById('category-filter');
    var statusFilter = document.getElementById('status-filter');
    var stockFilter = document.getElementById('stock-filter');
    var CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (!productModalEl) { console.error('Product modal missing'); return; }

    var productModal = new bootstrap.Modal(productModalEl);
    var restockModal = restockModalEl ? new bootstrap.Modal(restockModalEl) : null;
    var deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    var categoryModal = categoryModalEl ? new bootstrap.Modal(categoryModalEl) : null;
    var currentPage = 1;
    var isSubmitting = false;
    var isCatSubmitting = false;
    var deleteProductId = null;

    // ---------- Helpers ----------
    function showNotif(msg, type) {
        var c = document.getElementById('prodAlert');
        if (!c) {
            c = document.createElement('div');
            c.id = 'prodAlert';
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

    function showFormAlert(el, msg) {
        if (!el) return;
        el.classList.remove('d-none');
        el.textContent = msg;
    }

    function hideFormAlert(el) {
        if (el) el.classList.add('d-none');
    }

    function setBtnLoading(btn, textEl, loading, loadingText) {
        if (!btn) return;
        btn.disabled = loading;
        var sp = btn.querySelector('.spinner-border');
        if (sp) sp.classList.toggle('d-none', !loading);
        if (textEl) textEl.textContent = loading ? (loadingText || 'Saving...') : 'Save Product';
    }

    // ---------- Load data ----------
    function loadCategories(callback) {
        fetch('{{ route('super-admin.inventory.categories.data') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var cats = data.data || [];
                var filterEl = document.getElementById('category-filter');
                var prodCatEl = document.getElementById('product-category');
                if (filterEl) {
                    filterEl.innerHTML = '<option value="">All Categories</option>';
                    cats.forEach(function(c) {
                        filterEl.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>';
                    });
                }
                if (prodCatEl) {
                    prodCatEl.innerHTML = '<option value="">Select Category</option>';
                    cats.forEach(function(c) {
                        prodCatEl.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>';
                    });
                }
                if (callback) callback();
            });
    }

    function loadProducts(page) {
        page = page || 1;
        currentPage = page;
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading...</td></tr>';

        var params = new URLSearchParams({
            page: page,
            category_id: catFilter ? catFilter.value : '',
            status: statusFilter ? statusFilter.value : '',
            search: searchInput ? searchInput.value : '',
            stock: stockFilter ? stockFilter.value : '',
        });

        fetch('{{ route('super-admin.inventory.products.data') }}?' + params.toString())
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderProducts(data.data || []);
                renderPagination(data.meta || {});
            })
            .catch(function() {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Failed to load products.</td></tr>';
            });
    }

    function loadStats() {
        fetch('{{ route('super-admin.inventory.products.stats') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success || !data.data) return;
                var stats = data.data;
                var total = document.getElementById('total-products');
                var low = document.getElementById('low-stock');
                var out = document.getElementById('out-of-stock');
                var featured = document.getElementById('featured-count');
                if (total) total.textContent = stats.total || 0;
                if (low) low.textContent = stats.low_stock || 0;
                if (out) out.textContent = stats.out_of_stock || 0;
                if (featured) featured.textContent = stats.featured || 0;
            });
    }

    function renderProducts(products) {
        if (!tbody) return;
        if (!products.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No products found.</td></tr>';
            return;
        }

        tbody.innerHTML = products.map(function(p) {
            var catName = p.category ? p.category.name : '—';
            var stockClass = p.quantity <= 0 ? 'stock-out' : (p.quantity <= (p.low_stock_alert_level || 5) ? 'stock-low' : 'stock-ok');
            var creator = p.creator ? p.creator.name : (p.created_by?.name || '—');
            var imgHtml;
            if (p.image_url) {
                imgHtml = '<img src="' + p.image_url + '" class="product-image-preview me-2" alt="' + p.name + '">';
            } else {
                imgHtml = '<div class="product-image-wrapper me-2">' + (p.name ? p.name.charAt(0).toUpperCase() : 'P') + '</div>';
            }

            return '<tr data-id="' + p.id + '">' +
                '<td><div class="d-flex align-items-center">' +
                    imgHtml +
                    '<div><div class="fw-semibold">' + p.name + '</div>' +
                    '<small class="text-muted">' + (p.short_description || '') + '</small></div></div></td>' +
                '<td>' + catName + '</td>' +
                '<td><code>' + p.sku + '</code></td>' +
                '<td class="fw-bold">TSh ' + Number(p.selling_price).toLocaleString() + '</td>' +
                '<td><span class="badge stock-badge ' + stockClass + '">' + p.quantity + ' units</span></td>' +
                '<td><span class="badge ' + (p.status === 'active' ? 'bg-success' : 'bg-secondary') + '">' + p.status + '</span></td>' +
                '<td>' + (p.is_featured ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>') + '</td>' +
                '<td>' + creator + '</td>' +
                '<td><div class="d-flex gap-1">' +
                    '<button class="btn btn-sm btn-sa-outline btn-outline-primary edit-prod-btn" data-id="' + p.id + '" title="Edit"><i class="bi bi-pencil"></i></button>' +
                    '<button class="btn btn-sm btn-sa-outline btn-outline-success restock-prod-btn" data-id="' + p.id + '" data-name="' + p.name + '" data-qty="' + p.quantity + '" title="Restock"><i class="bi bi-plus-circle"></i></button>' +
                    '<button class="btn btn-sm btn-sa-outline ' + (p.status === 'active' ? 'btn-outline-warning' : 'btn-outline-info') + ' toggle-prod-btn" data-id="' + p.id + '" title="' + (p.status === 'active' ? 'Deactivate' : 'Activate') + '"><i class="bi bi-' + (p.status === 'active' ? 'pause' : 'play') + '"></i></button>' +
                    '<button class="btn btn-sm btn-sa-outline btn-outline-danger del-prod-btn" data-id="' + p.id + '" title="Delete"><i class="bi bi-trash"></i></button>' +
                '</div></td></tr>';
        }).join('');
    }

    function renderPagination(meta) {
        var container = document.getElementById('pagination-container');
        if (!container) return;
        if (!meta || !meta.last_page || meta.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        var html = '<nav><ul class="pagination pagination-sm">';
        for (var i = 1; i <= meta.last_page; i++) {
            html += '<li class="page-item ' + (i === meta.current_page ? 'active' : '') + '"><a class="page-link page-link-btn" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
        html += '</ul></nav>';
        container.innerHTML = html;
    }

    // ---------- Event delegation: table actions ----------
    if (tbody) {
        tbody.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn || !btn.dataset.id) return;
            var id = btn.dataset.id;

            if (btn.classList.contains('edit-prod-btn')) {
                openProductModal(id);
            } else if (btn.classList.contains('restock-prod-btn')) {
                openRestockModal(id, btn.dataset.name, btn.dataset.qty);
            } else if (btn.classList.contains('toggle-prod-btn')) {
                toggleProductStatus(id);
            } else if (btn.classList.contains('del-prod-btn')) {
                deleteProductId = id;
                if (deleteModal) deleteModal.show();
            }
        });
    }

    // ---------- Pagination delegation ----------
    var paginationContainer = document.getElementById('pagination-container');
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            var link = e.target.closest('a.page-link-btn');
            if (!link || !link.dataset.page) return;
            e.preventDefault();
            loadProducts(parseInt(link.dataset.page));
        });
    }

    // ---------- Filters ----------
    var searchTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() { loadProducts(1); }, 300);
        });
    }
    if (catFilter) catFilter.addEventListener('change', function() { loadProducts(1); });
    if (statusFilter) statusFilter.addEventListener('change', function() { loadProducts(1); });
    if (stockFilter) stockFilter.addEventListener('change', function() { loadProducts(1); });

    var resetBtn = document.getElementById('resetFiltersBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (catFilter) catFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (stockFilter) stockFilter.value = '';
            loadProducts(1);
        });
    }

    // ---------- Add product button ----------
    var addBtn = document.getElementById('addProductBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            openProductModal(null);
        });
    }

    // ---------- Manage categories button ----------
    var manageCatBtn = document.getElementById('manageCatBtn');
    if (manageCatBtn) {
        manageCatBtn.addEventListener('click', function() {
            if (categoryModal) {
                var titleEl = document.getElementById('catModalTitle');
                if (titleEl) titleEl.textContent = 'Add Category';
                var form = document.getElementById('category-form');
                if (form) form.reset();
                var idInput = document.getElementById('cat-id');
                if (idInput) idInput.value = '';
                var activeCheck = document.getElementById('cat_active');
                if (activeCheck) activeCheck.checked = true;
                categoryModal.show();
            }
        });
    }

    // ---------- Open product modal ----------
    function openProductModal(productId) {
        var titleEl = document.getElementById('productModalTitle');
        var form = document.getElementById('product-form');
        var idInput = document.getElementById('product-id');
        var mainPreview = document.getElementById('main-image-preview');
        var galleryPreview = document.getElementById('gallery-preview');
        var formAlert = document.getElementById('productFormAlert');

        if (form) form.reset();
        if (idInput) idInput.value = '';
        if (mainPreview) mainPreview.innerHTML = '';
        if (galleryPreview) galleryPreview.innerHTML = '';
        hideFormAlert(formAlert);

        if (productId && titleEl) {
            titleEl.textContent = 'Edit Product';
            fetch('{{ route('super-admin.inventory.products.show', ['product' => ':id']) }}'.replace(':id', productId))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var p = data.product || data;
                    if (idInput) idInput.value = p.id || '';
                    // Map fields
                    var fieldMap = {
                        'name': 'p_name', 'sku': 'p_sku', 'brand': 'p_brand',
                        'short_description': 'p_short_desc', 'description': 'p_desc',
                        'buying_price': 'p_buy_price', 'selling_price': 'p_sell_price',
                        'quantity': 'p_qty', 'low_stock_alert_level': 'p_low_stock',
                        'barcode': 'p_barcode', 'status': 'p_status',
                        'category_id': 'product-category'
                    };
                    for (var key in fieldMap) {
                        var el = document.getElementById(fieldMap[key]);
                        if (el) el.value = p[key] !== undefined && p[key] !== null ? p[key] : '';
                    }
                    var featuredEl = document.getElementById('p_featured');
                    if (featuredEl) featuredEl.checked = !!p.is_featured;
                    if (p.image_url && mainPreview) {
                        mainPreview.innerHTML = '<img src="' + p.image_url + '" style="max-height:80px;border-radius:8px;" onerror="this.style.display=\'none\'">';
                    }
                    productModal.show();
                })
                .catch(function() {
                    showNotif('Failed to load product', 'error');
                });
        } else {
            if (titleEl) titleEl.textContent = 'Add New Product';
            productModal.show();
        }
    }

    // ---------- Submit product form ----------
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Product form submit');

            if (isSubmitting) return;
            var formAlert = document.getElementById('productFormAlert');
            hideFormAlert(formAlert);

            var id = document.getElementById('product-id')?.value || '';
            var btn = document.getElementById('saveProductBtn');
            var btnText = document.getElementById('saveProductBtnText');
            setBtnLoading(btn, btnText, true);
            isSubmitting = true;

            var formData = new FormData(productForm);
            var url = id ? '/super-admin/inventory/products/' + id : '/super-admin/inventory/products';
            if (id) formData.append('_method', 'PUT');

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
                console.log('Product save response:', result);
                setBtnLoading(btn, btnText, false);
                isSubmitting = false;

                if (result.ok && result.data.success) {
                    productModal.hide();
                    showNotif(result.data.message, 'success');
                    loadProducts(currentPage);
                    loadStats();
                } else if (result.status === 422 && result.data.errors) {
                    var msgs = [];
                    for (var f in result.data.errors) {
                        if (result.data.errors.hasOwnProperty(f)) msgs = msgs.concat(result.data.errors[f]);
                    }
                    showFormAlert(formAlert, msgs.join(' | '));
                } else {
                    showFormAlert(formAlert, result.data.message || 'Error saving product');
                }
            })
            .catch(function(err) {
                console.error('Product save error:', err);
                setBtnLoading(btn, btnText, false);
                isSubmitting = false;
                showFormAlert(formAlert, 'Network error. Please try again.');
            });
        });
    }

    // ---------- Open restock modal ----------
    function openRestockModal(id, name, qty) {
        var idInput = document.getElementById('restock-product-id');
        var nameEl = document.getElementById('restock-product-name');
        var stockEl = document.getElementById('restock-current-stock');
        var form = document.getElementById('restock-form');
        if (idInput) idInput.value = id;
        if (nameEl) nameEl.textContent = name || '';
        if (stockEl) stockEl.textContent = (qty || 0) + ' units';
        if (form) form.reset();
        if (restockModal) restockModal.show();
    }

    // ---------- Restock form ----------
    if (restockForm) {
        restockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var id = document.getElementById('restock-product-id')?.value;
            if (!id) return;
            var btn = document.getElementById('restockBtn');
            var sp = btn ? btn.querySelector('.spinner-border') : null;
            if (btn) btn.disabled = true;
            if (sp) sp.classList.remove('d-none');

            var formData = new FormData(restockForm);

            fetch('/super-admin/inventory/products/' + id + '/restock', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (btn) btn.disabled = false;
                if (sp) sp.classList.add('d-none');
                if (data.success) {
                    if (restockModal) restockModal.hide();
                    showNotif(data.message, 'success');
                    loadProducts(currentPage);
                    loadStats();
                } else {
                    showNotif(data.message || 'Error restocking', 'error');
                }
            })
            .catch(function() {
                if (btn) btn.disabled = false;
                if (sp) sp.classList.add('d-none');
                showNotif('Network error', 'error');
            });
        });
    }

    // ---------- Toggle status ----------
    function toggleProductStatus(id) {
        fetch('/super-admin/inventory/products/' + id + '/toggle-status', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                showNotif(data.message, 'success');
                loadProducts(currentPage);
            } else {
                showNotif(data.message || 'Error toggling status', 'error');
            }
        })
        .catch(function() {
            showNotif('Network error', 'error');
        });
    }

    // ---------- Delete confirmation ----------
    var confirmDelBtn = document.getElementById('confirmDelProductBtn');
    if (confirmDelBtn) {
        confirmDelBtn.addEventListener('click', function() {
            if (!deleteProductId) return;
            confirmDelBtn.disabled = true;
            var sp = confirmDelBtn.querySelector('.spinner-border');
            if (sp) sp.classList.remove('d-none');
            confirmDelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';

            fetch('/super-admin/inventory/products/' + deleteProductId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                confirmDelBtn.disabled = false;
                confirmDelBtn.innerHTML = '<span class="spinner-border spinner-border-sm d-none me-1"></span>Delete';
                sp = confirmDelBtn.querySelector('.spinner-border');
                if (sp) sp.classList.add('d-none');
                if (deleteModal) deleteModal.hide();

                if (data.success) {
                    showNotif(data.message, 'success');
                    loadProducts(currentPage);
                    loadStats();
                } else {
                    showNotif(data.message || 'Error deleting', 'error');
                }
                deleteProductId = null;
            })
            .catch(function() {
                confirmDelBtn.disabled = false;
                confirmDelBtn.innerHTML = '<span class="spinner-border spinner-border-sm d-none me-1"></span>Delete';
                if (deleteModal) deleteModal.hide();
                showNotif('Network error', 'error');
            });
        });
    }

    // ---------- Category form (inside products) ----------
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (isCatSubmitting) return;
            var alertEl = document.getElementById('catFormAlert');
            hideFormAlert(alertEl);
            var btn = document.getElementById('saveProdcutCatBtn');
            var sp = btn ? btn.querySelector('.spinner-border') : null;
            if (btn) btn.disabled = true;
            if (sp) sp.classList.remove('d-none');
            isCatSubmitting = true;

            var id = document.getElementById('cat-id')?.value || '';
            var name = document.getElementById('cat_name')?.value || '';
            var desc = document.getElementById('cat_desc')?.value || '';
            var active = document.getElementById('cat_active')?.checked || true;

            if (!name.trim()) {
                showFormAlert(alertEl, 'Category name is required.');
                if (btn) btn.disabled = false;
                if (sp) sp.classList.add('d-none');
                isCatSubmitting = false;
                return;
            }

            var formData = new FormData();
            formData.append('name', name);
            formData.append('description', desc);
            formData.append('is_active', active ? '1' : '0');

            var url = id ? '/super-admin/inventory/categories/' + id : '/super-admin/inventory/categories';
            if (id) formData.append('_method', 'PUT');

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
                if (btn) btn.disabled = false;
                if (sp) sp.classList.add('d-none');
                isCatSubmitting = false;

                if (result.ok && result.data.success) {
                    if (categoryModal) categoryModal.hide();
                    showNotif(result.data.message, 'success');
                    loadCategories();
                } else if (result.status === 422 && result.data.errors) {
                    var msgs = [];
                    for (var f in result.data.errors) {
                        if (result.data.errors.hasOwnProperty(f)) msgs = msgs.concat(result.data.errors[f]);
                    }
                    showFormAlert(alertEl, msgs.join(' | '));
                } else {
                    showFormAlert(alertEl, result.data.message || 'Error saving category');
                }
            })
            .catch(function() {
                if (btn) btn.disabled = false;
                if (sp) sp.classList.add('d-none');
                isCatSubmitting = false;
                showFormAlert(alertEl, 'Network error');
            });
        });
    }

    // ---------- Modal cleanup ----------
    if (productModalEl) {
        productModalEl.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open');
            var bd = document.querySelectorAll('.modal-backdrop');
            bd.forEach(function(b) { b.remove(); });
        });
    }

    // ---------- Image preview ----------
    var imageInput = document.getElementById('p_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            var file = e.target.files ? e.target.files[0] : null;
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(ev) {
                var preview = document.getElementById('main-image-preview');
                if (preview) preview.innerHTML = '<img src="' + ev.target.result + '" style="max-height:80px;border-radius:8px;">';
            };
            reader.readAsDataURL(file);
        });
    }

    // ---------- Init ----------
    loadCategories(function() {
        loadProducts(1);
        loadStats();
    });
    console.log('Product Management JS initialized.');
})();
</script>
@endpush