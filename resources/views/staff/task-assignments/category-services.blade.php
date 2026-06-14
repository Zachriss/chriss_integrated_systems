@extends('layouts.app')

@section('title', $assignment->category->name . ' - Services')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('staff.task-assignments.dashboard') }}">My Tasks</a></li>
                    <li class="breadcrumb-item active">{{ $assignment->category->name }}</li>
                </ol>
            </nav>
            <h4 class="mb-0">
                <i class="bi bi-gear"></i> {{ $assignment->category->name }}
            </h4>
            @if($assignment->category->description)
                <p class="text-muted">{{ $assignment->category->description }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        @forelse($services as $service)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                @if($service->featured_image_url)
                <img src="{{ $service->featured_image_url }}" 
                     class="card-img-top" 
                     alt="{{ $service->name }}"
                     style="height: 180px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    @if($service->short_description)
                        <p class="card-text text-muted small">{{ $service->short_description }}</p>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        @if($service->base_price)
                            <span class="h6 mb-0 text-primary">TZS {{ number_format($service->base_price, 0) }}</span>
                        @endif
                        @if($service->duration_hours)
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> {{ $service->duration_hours }} hrs
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-box" style="font-size: 3rem; color: #ccc;"></i>
            <p class="mt-3 text-muted">No services found in this category.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection