@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>My Services</h5>
        <a href="{{ route('staff.service-requests') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-inbox me-1"></i> Customer Requests
        </a>
    </div>

    @forelse($grouped as $categoryName => $services)
    @php
        $catId = $services->first()->category_id ?? $services->first()->id;
        $hasCategoryRequests = $categoryHasRequests->contains($catId);
        $loopIndex = $loop->index;
    @endphp
    <div class="card border-0 shadow-sm mb-4 category-card">
        <div class="card-header bg-transparent d-flex align-items-center gap-2 py-3 category-header" 
             data-bs-toggle="collapse" data-bs-target="#catServices{{ $loopIndex }}" 
             role="button" aria-expanded="true">
            <i class="bi bi-chevron-down category-chevron"></i>
            <i class="bi bi-folder2-open text-primary"></i>
            <h6 class="mb-0 flex-grow-1">{{ $categoryName }}</h6>
            @if($hasCategoryRequests)
                <span class="badge bg-warning text-dark rounded-pill request-dot">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Requests
                </span>
            @endif
        </div>
        <div class="collapse show" id="catServices{{ $loopIndex }}">
            <div class="card-body">
                <div class="row g-3">
                    @foreach($services as $service)
                    @php
                        $reqCount = $serviceRequestCounts->get($service->id, 0);
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('staff.services.requests', $service) }}" class="text-decoration-none">
                            <div class="card service-card h-100 border">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-start gap-3 mb-2">
                                        @if($service->featured_image)
                                            <div class="flex-shrink-0" style="width: 48px; height: 48px; overflow: hidden; border-radius: 0.5rem;">
                                                <img src="{{ $service->featured_image_url }}" alt="{{ $service->name }}" class="w-100 h-100" style="object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 flex-shrink-0">
                                                <i class="bi bi-gear fs-5"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="mb-0 text-dark">{{ $service->name }}</h6>
                                            @if($service->short_description)
                                                <small class="text-muted d-block text-truncate">{{ $service->short_description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    @if($reqCount > 0)
                                        <div class="mb-2">
                                            <span class="badge bg-warning text-dark rounded-pill" title="Customer requests">
                                                <i class="bi bi-people-fill me-1"></i>{{ $reqCount }} request{{ $reqCount > 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="mb-2">
                                            <span class="badge bg-secondary text-light rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i>No requests
                                            </span>
                                        </div>
                                    @endif
                                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="fw-semibold text-success">TZS {{ number_format($service->base_price, 2) }}</span>
                                        <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-gear display-3"></i>
        <p class="mt-2">No services assigned yet.</p>
        <small>Services are assigned through tasks from your admin.</small>
    </div>
    @endforelse
</div>

@section('styles')
<style>
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .min-width-0 {
        min-width: 0;
    }
    .category-header {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s;
    }
    .category-header:hover {
        background-color: rgba(var(--color-primary-500-rgb), 0.04);
    }
    .category-header .category-chevron {
        transition: transform 0.25s ease;
    }
    .category-header[aria-expanded="false"] .category-chevron {
        transform: rotate(-90deg);
    }
    .request-dot {
        position: relative;
    }
</style>
@endsection
@endsection