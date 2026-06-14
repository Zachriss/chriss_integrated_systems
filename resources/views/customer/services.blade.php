@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>Browse Services</h5>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search services..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
            <a href="{{ route('customer.services.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    @if($services->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-gear display-3"></i>
            <p class="mt-2">No services available.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($services as $service)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                                <i class="bi bi-gear fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $service->name }}</h6>
                                <small class="text-muted">{{ $service->category->name ?? 'No category' }}</small>
                            </div>
                        </div>
                        @if($service->short_description)
                            <p class="text-muted small flex-grow-1">{{ Str::limit($service->short_description, 100) }}</p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                            <span class="fw-semibold text-success">TZS {{ number_format($service->base_price ?? 0, 2) }}</span>
                            <a href="{{ route('customer.services.show', $service) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-right me-1"></i> Select
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $services->links() }}</div>
    @endif
</div>
@endsection