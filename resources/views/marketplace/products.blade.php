@extends('layouts.app')

@section('title', 'Products - Marketplace')

@push('styles')
<style>
:root{--mp-primary:#0d6efd;--mp-bg:#f5f7fa;--mp-card-shadow:0 2px 12px rgba(0,0,0,0.06)}
body{background:var(--mp-bg)}
.mp-topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 0;position:sticky;top:0;z-index:100}
.mp-section{padding:32px 0}
.mp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
.mp-card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:var(--mp-card-shadow);transition:transform .2s,box-shadow .2s;position:relative}
.mp-card:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(0,0,0,0.1)}
.mp-card-img{width:100%;aspect-ratio:4/3;object-fit:cover;background:#eef2f7;display:block}
.mp-card-body{padding:12px 14px 14px}
.mp-card-cat{font-size:.68rem;text-transform:uppercase;color:#64748b;font-weight:700;letter-spacing:.04em}
.mp-card-title{font-weight:800;font-size:.92rem;color:#0f172a;margin:4px 0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mp-card-price{font-weight:900;color:var(--mp-primary);font-size:1.05rem}
.mp-chips{display:flex;gap:8px;flex-wrap:wrap}
.mp-chip{padding:6px 14px;border-radius:999px;border:1px solid #d1d5db;background:#fff;font-size:.8rem;font-weight:600;color:#374151;text-decoration:none;transition:all .15s}
.mp-chip:hover,.mp-chip.active{background:var(--mp-primary);color:#fff;border-color:var(--mp-primary)}
@media(max-width:575px){.mp-grid{grid-template-columns:repeat(2,1fr);gap:10px}}
</style>
@endpush

@section('content')
<div class="mp-topbar">
    <div class="container">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <h5 class="mb-0 fw-black">Products</h5>
            </div>
            <div class="col-md-5">
                <form method="GET" action="{{ route('marketplace.products') }}">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
                <form method="GET" action="{{ route('marketplace.products') }}">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Sort: Default</option>
                        <option value="price_asc" {{ request('sort')==='price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort')==='newest' ? 'selected' : '' }}>Newest</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="mp-section">
    <div class="container">
        <div class="mp-chips mb-3">
            <a href="{{ route('marketplace.products') }}" class="mp-chip {{ !request('category') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('marketplace.products', array_merge(request()->query(), ['category' => $cat->id])) }}" 
                   class="mp-chip {{ request('category') == $cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
            @endforeach
        </div>

        @if($products->isEmpty())
        <div class="text-center py-5"><i class="bi bi-box-seam display-4 text-muted"></i><h5 class="mt-3">No products found</h5><p class="text-muted">Try adjusting filters.</p></div>
        @else
        <div class="mp-grid">
            @foreach($products as $p)
            <a href="{{ route('shop.show', $p->slug ?? $p->id) }}" class="text-decoration-none">
            <div class="mp-card">
                <img src="{{ $p->image_url }}" class="mp-card-img" alt="{{ $p->name }}" onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Product'">
                <div class="mp-card-body">
                    @if($p->category)<div class="mp-card-cat">{{ $p->category->name }}</div>@endif
                    <div class="mp-card-title">{{ $p->name }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="mp-card-price">TZS {{ number_format($p->selling_price,0) }}</span>
                        <span class="small {{ $p->quantity>0?'text-success':'text-danger' }}">{{ $p->quantity>0?'In Stock':'Out' }}</span>
                    </div>
                </div>
            </div>
            </a>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
        @endif
    </div>
</section>
@endsection