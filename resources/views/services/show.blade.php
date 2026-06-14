@extends('layouts.app')

@section('title', $service->name . ' - ' . ($system_settings->system_name ?? 'Chriss Integrated Systems'))
@section('hide_default_footer', '1')

@push('critical-head')
<style>
    .service-detail-hero {
        padding: 72px 0 48px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
    }
    .gallery-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.15s ease;
    }
    .gallery-thumb:hover, .gallery-thumb.active { border-color: #0d6efd; }
    .detail-section { padding: 56px 0; }
    .detail-section:nth-child(even) { background: #f8fafc; }
</style>
@endpush

@section('content')
<section class="service-detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-white-50">Services</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $service->name }}</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h1 class="fw-bold mb-3">{{ $service->name }}</h1>
                @if($service->category)
                    <span class="badge bg-primary mb-2">{{ $service->category->name }}</span>
                @endif
                <p class="text-white-50 mb-4">{{ $service->short_description }}</p>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @if($service->base_price > 0)
                        <span class="fs-3 fw-bold text-white">TSh {{ number_format($service->base_price, 0) }}</span>
                    @endif
                    @if($service->duration_hours)
                        <span class="text-white-50"><i class="bi bi-clock me-1"></i> {{ $service->duration_hours }} hours</span>
                    @endif
                    @if($service->is_featured)
                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Featured</span>
                    @endif
                </div>
                <a href="{{ route('home') }}#booking" class="btn btn-primary btn-lg mt-4">
                    <i class="bi bi-calendar-check me-2"></i> Request This Service
                </a>
            </div>
            <div class="col-lg-6">
                <img src="{{ $service->featured_image_url }}" alt="{{ $service->name }}" 
                     class="img-fluid rounded shadow" style="max-height:380px;width:100%;object-fit:cover;"
                     onerror="this.src='https://placehold.co/800x500/e2e8f0/64748b?text=Service'">
            </div>
        </div>

        @if(!empty($service->gallery_images) && count($service->gallery_images) > 0)
        <div class="d-flex gap-2 mt-3 flex-wrap">
            @foreach($service->gallery_images as $img)
                <img src="{{ asset('storage/' . $img) }}" class="gallery-thumb" alt="Gallery"
                     onerror="this.style.display='none'">
            @endforeach
        </div>
        @endif
    </div>
</section>

<section class="detail-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4">Service Overview</h3>
                <div class="text-muted lh-lg">
                    {!! nl2br(e($service->description ?? 'No detailed description provided.')) !!}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Quick Info</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-tag text-primary me-2"></i> <strong>Category:</strong> {{ $service->category?->name ?? '-' }}</li>
                            <li class="mb-2"><i class="bi bi-currency-dollar text-primary me-2"></i> <strong>Price:</strong> @if($service->base_price > 0) TSh {{ number_format($service->base_price, 0) }} @else Price on request @endif</li>
                            <li class="mb-2"><i class="bi bi-clock text-primary me-2"></i> <strong>Duration:</strong> {{ $service->duration_hours ? $service->duration_hours . ' hour(s)' : 'Varies' }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i> <strong>Status:</strong> {{ ucfirst($service->status ?? 'active') }}</li>
                        </ul>
                        <hr>
                        <a href="{{ route('home') }}#booking" class="btn btn-primary w-100">
                            <i class="bi bi-calendar-check me-2"></i> Request Service
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($relatedServices->isNotEmpty())
<section class="detail-section">
    <div class="container">
        <h3 class="fw-bold mb-4">Related Services</h3>
        <div class="row g-4">
            @foreach($relatedServices as $related)
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <img src="{{ $related->featured_image_url }}" class="card-img-top" alt="{{ $related->name }}"
                         style="height:140px;object-fit:cover;"
                         onerror="this.src='https://placehold.co/400x300/e2e8f0/64748b?text=Service'">
                    <div class="card-body">
                        <h6 class="fw-bold">{{ $related->name }}</h6>
                        @if($related->base_price > 0)
                            <strong class="text-primary">TSh {{ number_format($related->base_price, 0) }}</strong>
                        @endif
                        <a href="{{ route('services.show', $related->slug) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">View</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection