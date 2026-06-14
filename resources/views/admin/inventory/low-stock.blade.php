@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Low Stock Products</h4>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($products->isEmpty())
    <div class="alert alert-success" role="alert">
        <i class="bi bi-check-circle me-2"></i> All products are sufficiently stocked.
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Products Requiring Attention</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Alert Level</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td><span class="badge bg-warning">{{ $product->quantity }}</span></td>
                            <td>{{ $product->low_stock_alert_level }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.inventory.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Manage
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection