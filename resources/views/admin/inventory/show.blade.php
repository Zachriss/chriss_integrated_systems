@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $product->name }}</h4>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Product Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>SKU:</strong> {{ $product->sku }}</div>
                    <div class="mb-2"><strong>Current Stock:</strong> 
                        @if($product->isLowStock())
                        <span class="badge bg-warning">{{ $product->quantity }}</span>
                        @else
                        {{ $product->quantity }}
                        @endif
                    </div>
                    <div class="mb-2"><strong>Buying Price:</strong> KES {{ number_format($product->buying_price, 2) }}</div>
                    <div class="mb-2"><strong>Selling Price:</strong> KES {{ number_format($product->selling_price, 2) }}</div>
                    <div class="mb-2"><strong>Alert Level:</strong> {{ $product->low_stock_alert_level }}</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Stock Adjustment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.inventory.stock-in', $product->id) }}" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Stock In (Add Quantity)</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle me-1"></i> Add Stock
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.inventory.stock-out', $product->id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Stock Out (Remove Quantity)</label>
                            <input type="number" name="quantity" class="form-control" min="1" max="{{ $product->quantity }}" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Remove stock from this product?')">
                            <i class="bi bi-dash-circle me-1"></i> Remove Stock
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Sales</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $saleItem)
                                <tr>
                                    <td>{{ $saleItem->sale->created_at->format('M d, Y') }}</td>
                                    <td>{{ $saleItem->sale->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $saleItem->quantity }}</td>
                                    <td class="text-end">KES {{ number_format($saleItem->total_price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No sales recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sales->hasPages())
                <div class="card-footer bg-white">{{ $sales->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection