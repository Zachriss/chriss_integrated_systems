@extends('layouts.app')

@section('title', 'Services - Marketplace')

@push('styles')
<style>
:root{--mp-primary:#0d6efd;--mp-bg:#f5f7fa;--mp-card-shadow:0 2px 12px rgba(0,0,0,0.06)}
body{background:var(--mp-bg)}
.mp-topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 0;position:sticky;top:0;z-index:100}
.mp-section{padding:32px 0}
.mp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
.mp-card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:var(--mp-card-shadow);transition:transform .2s,box-shadow .2s;position:relative}
.mp-card:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(0,0,0,0.1)}
.mp-card-img{width:100%;aspect-ratio:16/9;object-fit:cover;background:#eef2f7;display:block}
.mp-card-body{padding:12px 14px 14px}
.mp-card-cat{font-size:.68rem;text-transform:uppercase;color:#64748b;font-weight:700;letter-spacing:.04em}
.mp-card-title{font-weight:800;font-size:.92rem;color:#0f172a;margin:4px 0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mp-card-price{font-weight:900;color:var(--mp-primary);font-size:1.05rem}
.mp-chips{display:flex;gap:8px;flex-wrap:wrap}
.mp-chip{padding:6px 14px;border-radius:999px;border:1px solid #d1d5db;background:#fff;font-size:.8rem;font-weight:600;color:#374151;text-decoration:none;transition:all .15s}
.mp-chip:hover,.mp-chip.active{background:var(--mp-primary);color:#fff;border-color:var(--mp-primary)}
.mp-card .btn{font-size:.78rem;border-radius:8px;font-weight:700}
@media(max-width:575px){.mp-grid{grid-template-columns:repeat(2,1fr);gap:10px}}
</style>
@endpush

@section('content')
<div class="mp-topbar">
    <div class="container">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 fw-black">Services</h5>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('marketplace.services') }}">
                    @if(request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search services..." value="{{ request('search') }}">
                        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="mp-section">
    <div class="container">
        <div class="mp-chips mb-3">
            <a href="{{ route('marketplace.services') }}" class="mp-chip {{ !request('category_id') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('marketplace.services', array_merge(request()->query(), ['category_id' => $cat->id])) }}" 
                   class="mp-chip {{ request('category_id') == $cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
            @endforeach
        </div>

        @if($services->isEmpty())
        <div class="text-center py-5"><i class="bi bi-gear display-4 text-muted"></i><h5 class="mt-3">No services found</h5><p class="text-muted">Try adjusting filters.</p></div>
        @else
        <div class="mp-grid">
            @foreach($services as $sv)
            <div class="mp-card">
                <img src="{{ $sv->featured_image_url }}" class="mp-card-img" alt="{{ $sv->name }}" onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Service'">
                <div class="mp-card-body">
                    @if($sv->category)<div class="mp-card-cat">{{ $sv->category->name }}</div>@endif
                    <div class="mp-card-title">{{ $sv->name }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                        <span class="mp-card-price">TZS {{ number_format($sv->base_price??0,0) }}</span>
                        <span class="small text-muted">{{ $sv->duration_hours ? $sv->duration_hours.'h' : '' }}</span>
                    </div>
                    <a href="{{ route('services.show', $sv->slug) }}" class="btn btn-outline-primary btn-sm w-100">Book Service</a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $services->links() }}</div>
        @endif
    </div>
</section>
@endsection