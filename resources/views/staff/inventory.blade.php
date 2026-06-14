@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <h5 class="mb-3">Inventory Products</h5>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($products->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-box display-3"></i>
                    <p class="mt-2">No products available.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->description)
                                        <br><small class="text-muted">{{ Str::limit($product->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>TZS {{ number_format($product->price, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->quantity > 10 ? 'success' : ($product->quantity > 0 ? 'warning' : 'danger') }}">
                                        {{ $product->quantity }} in stock
                                    </span>
                                </td>
                                <td>
                                    @if($product->quantity > 0)
                                        <button class="btn btn-sm btn-outline-danger stock-out-btn"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}"
                                                data-max="{{ $product->quantity }}">
                                            <i class="bi bi-dash-circle"></i> Reduce
                                        </button>
                                    @else
                                        <span class="text-muted small">Out of stock</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Stock-out Modal --}}
<div class="modal fade" id="stockOutModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Reduce Stock: <span id="stockOutProductName"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockOutForm">
                <input type="hidden" id="stockOutProductId">
                <div class="modal-body">
                    <label class="form-label">Quantity to remove</label>
                    <input type="number" id="stockOutQty" class="form-control" min="1" required>
                    <small class="text-muted" id="stockOutMax"></small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="stockOutSubmit">Remove</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const stockModal = new bootstrap.Modal('#stockOutModal');
let currentProductId = null;

document.querySelectorAll('.stock-out-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        currentProductId = this.dataset.productId;
        document.getElementById('stockOutProductName').textContent = this.dataset.productName;
        document.getElementById('stockOutProductId').value = this.dataset.productId;
        document.getElementById('stockOutQty').max = this.dataset.max;
        document.getElementById('stockOutQty').value = 1;
        document.getElementById('stockOutMax').textContent = 'Max: ' + this.dataset.max;
        stockModal.show();
    });
});

document.getElementById('stockOutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('stockOutSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Removing...';

    const qty = document.getElementById('stockOutQty').value;
    fetch('{{ route("staff.inventory.stock-out", "_ID_") }}'.replace('_ID_', currentProductId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = 'Remove';
        if (data.success) {
            if (typeof showSystemAlert === 'function') {
                showSystemAlert({ theme: 'success', title: 'Stock Updated', text: data.message, timer: 2000, showConfirmButton: false });
            }
            stockModal.hide();
            setTimeout(() => location.reload(), 800);
        } else {
            alert(data.message || 'Error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = 'Remove';
        alert('An error occurred.');
    });
});
</script>
@endsection