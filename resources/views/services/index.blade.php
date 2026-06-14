@extends('layouts.app')

@section('title', 'Services - ' . ($system_settings->system_name ?? 'Chriss Integrated Systems'))
@section('hide_default_footer', '1')

@push('critical-head')
<style>
    .services-hero {
        padding: 80px 0 56px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
    }
    .services-hero h1 { font-weight: 900; font-size: clamp(2rem, 4vw, 3rem); }
    .services-hero p { color: rgba(255,255,255,0.75); max-width: 640px; }
    .service-section { padding: 56px 0; }
    .service-section:nth-child(even) { background: #f8fafc; }
    .section-title { font-weight: 900; margin-bottom: 32px; }
    .service-card-modern {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .service-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    }
    .service-card-modern .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    .filter-btn {
        border-radius: 999px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        transition: all 0.15s ease;
    }
    .filter-btn:hover, .filter-btn.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .pagination-wrap { display: flex; justify-content: center; margin-top: 32px; }
</style>
@endpush

@section('content')
<section class="services-hero">
    <div class="container">
        <h1>Our Services</h1>
        <p>Browse our complete range of professional technology services. From software development to repairs, we have you covered.</p>
    </div>
</section>

<section class="service-section">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <a href="{{ route('services.index') }}" class="filter-btn {{ !request('category') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('services.index', ['category' => $cat->slug]) }}" 
                   class="filter-btn {{ request('category') === $cat->slug ? 'active' : '' }}">
                    @if($cat->icon) <i class="bi {{ $cat->icon }} me-1"></i> @endif
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if($featuredServices->isNotEmpty())
        <div class="mb-5">
            <h5 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Featured Services</h5>
            <div class="row g-4">
                @foreach($featuredServices->take(3) as $service)
                <div class="col-md-4">
                    <div class="service-card-modern position-relative">
                        <img src="{{ $service->featured_image_url }}" class="card-img-top" alt="{{ $service->name }}"
                             onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Service'">
                        <div class="p-3">
                            <span class="badge bg-warning text-dark mb-2"><i class="bi bi-star-fill me-1"></i>Featured</span>
                            <h5 class="fw-bold">{{ $service->name }}</h5>
                            <p class="small text-muted">{{ Str::limit($service->short_description ?? $service->description, 120) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-primary">
                                    @if($service->base_price > 0) TSh {{ number_format($service->base_price, 0) }} @else Price on request @endif
                                </span>
                                <span class="small text-muted">{{ $service->duration_hours ? $service->duration_hours . ' hrs' : '' }}</span>
                            </div>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <h5 class="fw-bold mb-3">All Services</h5>
        <div class="row g-4">
            @forelse($services as $service)
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="service-card-modern">
                    <img src="{{ $service->featured_image_url }}" class="card-img-top" alt="{{ $service->name }}"
                         onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Service'">
                    <div class="p-3">
                        <small class="text-primary fw-bold text-uppercase" style="font-size:0.7rem;letter-spacing:0.05em;">
                            @if($service->category){{ $service->category->name }}@endif
                        </small>
                        <h6 class="fw-bold mt-1">{{ $service->name }}</h6>
                        <p class="small text-muted mb-2">{{ Str::limit($service->short_description ?? '', 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-primary">
                                @if($service->base_price > 0) TSh {{ number_format($service->base_price, 0) }} @else - @endif
                            </strong>
                            @if($service->is_featured)
                                <span class="badge bg-warning text-dark" style="font-size:0.65rem;">Featured</span>
                            @endif
                        </div>
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox" style="font-size:3rem;color:#cbd5e1;"></i>
                <p class="text-muted mt-3">No services available at the moment. Check back later.</p>
            </div>
            @endforelse
        </div>

        <div class="pagination-wrap">
            {{ $services->withQueryString()->links() }}
        </div>
    </div>
</section>
@endsection