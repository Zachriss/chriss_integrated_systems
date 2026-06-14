@extends('layouts.app')

@section('title', 'Shop - Products & Services')

@push('styles')
<style>
    .shop-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .product-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .product-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #f3f4f6;
        transition: transform 0.3s ease;
    }
    .product-card:hover img {
        transform: scale(1.05);
    }
    .product-card .card-body {
        padding: 1rem;
    }
    .filter-wrapper {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 2rem;
        border: 1px solid #e5e7eb;
    }
    .stock-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 999px;
    }
    .featured-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: #f59e0b;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        z-index: 2;
    }
    .shop-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255,255,255,0.15);
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.85rem;
    }
    .category-pills {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .category-pill {
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.2s;
    }
    .category-pill:hover, .category-pill.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    @media (max-width: 575.98px) {
        .product-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="shop-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="mb-2">Our Products</h1>
                <p class="mb-0 opacity-90">Browse our inventory of quality products and services</p>
            </div>
            <div class="d-flex gap-2">
                <span class="shop-stat">
                    <i class="bi bi-box-seam"></i> {{ $products->total() }} Products
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container mb-4">
    <div class="filter-wrapper">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" id="search-products"
                           placeholder="Search products..." value="{{ request('search') }}">
                    @if(request('search'))
                    <button class="btn btn-outline-secondary" onclick="clearSearch()">
                        <i class="bi bi-x"></i>
                    </button>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="category-filter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="stock-filter">
                    <option value="">All Stock</option>
                    <option value="in-stock" {{ request('stock') === 'in-stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low-stock" {{ request('stock') === 'low-stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out-of-stock" {{ request('stock') === 'out-of-stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" onclick="filterProducts()">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </div>
        @if(request('search') || request('category') || request('stock'))
        <div class="mt-2">
            <a href="{{ route('shop.index') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-clockwise"></i> Clear all filters
            </a>
        </div>
        @endif
    </div>

    @if($categories->count() > 0)
    <div class="category-pills">
        <a href="{{ route('shop.index') }}" class="category-pill {{ !request('category') ? 'active' : '' }}">All</a>
        @foreach($categories as $cat)
            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
               class="category-pill {{ request('category') == $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
    @endif

    @if($products->count() > 0)
    <div class="product-grid" id="product-grid">
        @foreach($products as $product)
        <div class="product-card position-relative">
            @if($product->is_featured)
                <span class="featured-badge"><i class="bi bi-star-fill"></i> Featured</span>
            @endif
            <div class="overflow-hidden">
                <img src="{{ $product->image_url }}"
                     class="card-img-top"
                     alt="{{ $product->name }}"
                     onerror="this.src='https://placehold.co/400x400/e2e8f0/64748b?text=Product'">
            </div>
            <div class="card-body d-flex flex-column">
                @if($product->category)
                    <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; font-weight: 700;">
                        {{ $product->category->name }}
                    </small>
                @endif
                <h5 class="fw-bold mt-1 mb-2" style="font-size: 1rem;">
                    <a href="{{ route('shop.show', $product->slug ?? $product->id) }}" class="text-decoration-none text-dark stretched-link">
                        {{ Str::limit($product->name, 50) }}
                    </a>
                </h5>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-primary fs-5">
                            TSh {{ number_format($product->selling_price, 0) }}
                        </strong>
                        <span class="stock-badge {{ $product->quantity <= 0 ? 'bg-danger text-white' : ($product->quantity <= $product->low_stock_alert_level ? 'bg-warning text-dark' : 'bg-success text-white') }}">
                            {{ $product->quantity <= 0 ? 'Out of Stock' : ($product->quantity <= $product->low_stock_alert_level ? 'Low Stock' : 'In Stock') }}
                        </span>
                    </div>
                    <a href="{{ route('shop.show', $product->slug ?? $product->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-eye me-1"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-5">
        <i class="bi bi-box-seam display-4 text-muted"></i>
        <h4 class="mt-3">No products found</h4>
        <p class="text-muted">
            @if(request('search') || request('category') || request('stock'))
                Try adjusting your search or filter criteria.
            @else
                Check back later for new products!
            @endif
        </p>
        @if(request('search') || request('category') || request('stock'))
        <a href="{{ route('shop.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-clockwise me-1"></i> View All Products
        </a>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterProducts() {
    const search = document.getElementById('search-products').value;
    const category = document.getElementById('category-filter').value;
    const stock = document.getElementById('stock-filter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (category) params.set('category', category);
    if (stock) params.set('stock', stock);
    window.location = '{{ route('shop.index') }}' + (params.toString() ? '?' + params : '');
}

function clearSearch() {
    document.getElementById('search-products').value = '';
    filterProducts();
}

document.getElementById('search-products').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') filterProducts();
});
</script>
@endpush