@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5><i class="bi bi-box-seam me-2"></i>Browse Products</h5>
    </div>

    {{-- Product Catalog --}}
    @if($products->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-box display-3"></i>
            <p class="mt-2">No products available for order at this time.</p>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $product->category->name ?? '—' }}</small>
                                @if($product->quantity <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->quantity <= ($product->low_stock_alert_level ?? 5))
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </div>
                            <h6 class="mt-2 mb-0">{{ $product->name }}</h6>
                        </div>
                        <div class="card-body pt-2">
                            @if($product->short_description)
                                <p class="small text-muted mb-2">{{ Str::limit($product->short_description, 80) }}</p>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <strong class="text-primary">TZS {{ number_format($product->selling_price, 0) }}</strong>
                                <small class="text-muted">Qty: {{ $product->quantity }}</small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <button class="btn btn-primary btn-sm w-100 order-product-btn"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-price="{{ $product->selling_price }}"
                                    {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                <i class="bi bi-cart-plus me-1"></i> Order
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>

{{-- Order Modal --}}
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="orderForm">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="orderProductId">
                    <div class="mb-3">
                        <h6 id="orderProductName" class="fw-bold"></h6>
                        <span id="orderProductPrice" class="text-primary"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any special instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="orderSubmitBtn">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Order buttons
    document.querySelectorAll('.order-product-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('orderProductId').value = this.dataset.productId;
            document.getElementById('orderProductName').textContent = this.dataset.productName;
            document.getElementById('orderProductPrice').textContent = 'TZS ' + Number(this.dataset.productPrice).toLocaleString();
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        });
    });

    // Submit order
    document.getElementById('orderForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('orderSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Ordering...';

        const data = Object.fromEntries(new FormData(this).entries());

        fetch('{{ route("customer.products.order") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({theme: 'success', title: 'Order Placed!', text: res.message, timer: 2000});
                }
                bootstrap.Modal.getInstance(document.getElementById('orderModal'))?.hide();
                document.getElementById('orderForm').reset();
            } else {
                alert(res.message || 'Error placing order');
                btn.disabled = false;
                btn.innerHTML = 'Place Order';
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = 'Place Order';
            alert('Error placing order');
        });
    });
});
</script>
@endsection