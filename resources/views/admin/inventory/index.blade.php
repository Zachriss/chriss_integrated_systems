@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Inventory Management</h4>
        <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-outline-warning">
            <i class="bi bi-exclamation-triangle me-1"></i> Low Stock Alert
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Assigned Products</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>
                                @if($product->isLowStock())
                                <span class="badge bg-warning">{{ $product->quantity }}</span>
                                @else
                                {{ $product->quantity }}
                                @endif
                            </td>
                            <td>KES {{ number_format($product->buying_price, 2) }}</td>
                            <td>KES {{ number_format($product->selling_price, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.inventory.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No products assigned for inventory management.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-white">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection