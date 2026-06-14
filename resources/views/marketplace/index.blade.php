@extends('layouts.app')

@section('title', $system_settings->system_name . ' - Marketplace')

@push('styles')
<style>
:root{--mp-primary:#0d6efd;--mp-bg:#f5f7fa;--mp-card-shadow:0 2px 12px rgba(0,0,0,0.06);}
body{background:var(--mp-bg)}
.mp-hero{background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);padding:56px 0 48px;color:#fff;}
.mp-search{max-width:640px;margin:16px auto 0}.mp-search .input-group{border-radius:12px;overflow:hidden;background:#fff}
.mp-search .form-control{border:0;padding:14px 18px;font-size:1rem}.mp-search .btn{border:0;padding:0 24px;font-weight:700}
.mp-section{padding:44px 0}.mp-section h2{font-weight:900;font-size:1.5rem;margin-bottom:20px}
.mp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
.mp-card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:var(--mp-card-shadow);transition:transform .2s,box-shadow .2s;position:relative}
.mp-card:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(0,0,0,0.1)}
.mp-card-img{width:100%;aspect-ratio:4/3;object-fit:cover;background:#eef2f7;display:block}
.mp-card-body{padding:12px 14px 14px}
.mp-card-cat{font-size:.68rem;text-transform:uppercase;color:#64748b;font-weight:700;letter-spacing:.04em}
.mp-card-title{font-weight:800;font-size:.92rem;color:#0f172a;margin:4px 0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mp-card-price{font-weight:900;color:var(--mp-primary);font-size:1.05rem}
.mp-card-badge{position:absolute;top:10px;right:10px;font-size:.68rem;font-weight:700;padding:4px 8px;border-radius:6px;background:rgba(255,255,255,.92)}
.mp-card .btn{font-size:.78rem;border-radius:8px;font-weight:700}
.mp-chips{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
.mp-chip{padding:6px 14px;border-radius:999px;border:1px solid #d1d5db;background:#fff;font-size:.8rem;font-weight:600;color:#374151;text-decoration:none;transition:all .15s}
.mp-chip:hover,.mp-chip.active{background:var(--mp-primary);color:#fff;border-color:var(--mp-primary)}
.service-card .mp-card-img{aspect-ratio:16/9}
@media(max-width:575px){.mp-grid{grid-template-columns:repeat(2,1fr);gap:10px}}
</style>
@endpush

@section('content')
<section class="mp-hero">
    <div class="container text-center">
        <span class="badge bg-light text-dark mb-2 px-3 py-2 rounded-pill"><i class="bi bi-stars me-1"></i> Marketplace</span>
        <h1 class="fw-black display-5 mb-2">Find what you need</h1>
        <p class="text-white-50">Browse products and services — all from local inventory.</p>
        <form class="mp-search" action="{{ route('marketplace.products') }}" method="GET">
            <div class="input-group shadow">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search products & services..." autocomplete="off" id="mpSearchInput">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
            <div id="searchResults" class="mt-2 text-start d-none"></div>
        </form>
    </div>
</section>

<section class="mp-section">
    <div class="container">
        <h2><i class="bi bi-star-fill text-warning me-2"></i> Featured Products</h2>
        @if($featuredProducts->isEmpty())<p class="text-muted">No featured products yet.</p>@else
        <div class="mp-grid">
            @foreach($featuredProducts as $p)
            <div class="mp-card">
                <img src="{{ $p->image_url }}" class="mp-card-img" alt="{{ $p->name }}" onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Product'">
                @if($p->is_featured)<span class="mp-card-badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Featured</span>@endif
                <div class="mp-card-body">
                    @if($p->category)<div class="mp-card-cat">{{ $p->category->name }}</div>@endif
                    <div class="mp-card-title">{{ $p->name }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="mp-card-price">TZS {{ number_format($p->selling_price,0) }}</span>
                        <span class="small {{ $p->quantity>0?'text-success':'text-danger' }}">{{ $p->quantity>0?'In Stock':'Out' }}</span>
                    </div>
                    <a href="{{ route('shop.show', $p->slug ?? $p->id) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">View</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<section class="mp-section" style="background:#fff">
    <div class="container">
        <h2><i class="bi bi-gear-wide-connected text-primary me-2"></i> Featured Services</h2>
        @if($featuredServices->isEmpty())<p class="text-muted">No featured services yet.</p>@else
        <div class="mp-grid">
            @foreach($featuredServices as $sv)
            <div class="mp-card service-card">
                <img src="{{ $sv->featured_image_url }}" class="mp-card-img" alt="{{ $sv->name }}" onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Service'">
                <div class="mp-card-body">
                    @if($sv->category)<div class="mp-card-cat">{{ $sv->category->name }}</div>@endif
                    <div class="mp-card-title">{{ $sv->name }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="mp-card-price">TZS {{ number_format($sv->base_price??0,0) }}</span>
                        <span class="small text-muted">{{ $sv->duration_hours ? $sv->duration_hours.'h' : '' }}</span>
                    </div>
                    <a href="{{ route('services.show', $sv->slug) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">Book Service</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<section class="mp-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="bi bi-clock-history me-2"></i> Latest Products</h2>
            <a href="{{ route('marketplace.products') }}" class="btn btn-sm btn-outline-primary">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        @if($latestProducts->isEmpty())<p class="text-muted">No products yet.</p>@else
        <div class="mp-grid">
            @foreach($latestProducts as $p)
            <div class="mp-card">
                <img src="{{ $p->image_url }}" class="mp-card-img" alt="{{ $p->name }}" onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Product'">
                <div class="mp-card-body">
                    @if($p->category)<div class="mp-card-cat">{{ $p->category->name }}</div>@endif
                    <div class="mp-card-title">{{ $p->name }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="mp-card-price">TZS {{ number_format($p->selling_price,0) }}</span>
                        <span class="small {{ $p->quantity>0?'text-success':'text-danger' }}">{{ $p->quantity>0?'In Stock':'Out' }}</span>
                    </div>
                    <a href="{{ route('shop.show', $p->slug ?? $p->id) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">View</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
const input = document.getElementById('mpSearchInput');
const results = document.getElementById('searchResults');
let timer;
input.addEventListener('input', function(){
    clearTimeout(timer);
    const q = this.value.trim();
    if(q.length < 2){ results.classList.add('d-none'); return; }
    timer = setTimeout(() => {
        fetch('{{ route("marketplace.search") }}?q=' + encodeURIComponent(q))
        .then(r => r.json()).then(d => {
            results.classList.remove('d-none');
            if(!d.results || d.results.length === 0){
                results.innerHTML = '<div class="text-muted p-2 small">No results found.</div>'; return;
            }
            let html = '<div class="list-group">';
            d.results.forEach(r => {
                const icon = r.type === 'product' ? 'bi-box-seam' : 'bi-gear';
                html += `<a href="${r.url}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0">
                    <i class="bi ${icon} text-primary"></i>
                    <div><strong class="small">${r.name}</strong><br><small class="text-muted">${r.type} · TZS ${r.price?.toLocaleString()}</small></div>
                </a>`;
            });
            html += '</div>';
            results.innerHTML = html;
        });
    }, 300);
});
document.addEventListener('click', (e) => { if(!e.target.closest('.mp-search')) results.classList.add('d-none'); });
</script>
@endpush