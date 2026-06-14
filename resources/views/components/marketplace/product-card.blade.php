<div class="product-card h-100">
    <div class="position-relative overflow-hidden" style="aspect-ratio: 1/1; background: #f3f4f6;">
        <img src="{{ $item['image'] ?? 'https://placehold.co/400x400/e2e8f0/64748b?text=Product' }}"
             class="card-img-top h-100 w-100"
             style="object-fit: cover; transition: transform 0.3s ease;"
             alt="{{ $item['name'] ?? '' }}"
             onerror="this.src='https://placehold.co/400x400/e2e8f0/64748b?text=Product'">
        @if(!empty($item['is_featured']))
            <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                <i class="bi bi-star-fill me-1"></i> Featured
            </span>
        @endif
        @if(isset($item['quantity']) && $item['quantity'] <= 0)
            <span class="position-absolute top-0 start-0 m-2 badge bg-danger">
                Out of Stock
            </span>
        @elseif(isset($item['quantity']) && $item['quantity'] <= ($item['low_stock_alert_level'] ?? 5))
            <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark">
                <i class="bi bi-exclamation-triangle me-1"></i> Low Stock
            </span>
        @endif
    </div>
    <div class="card-body d-flex flex-column p-3">
        @if(!empty($item['category_name']))
            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; font-weight: 700;">
                {{ $item['category_name'] }}
            </small>
        @endif
        <h6 class="fw-bold mt-1 mb-2" style="font-size: 0.95rem; line-height: 1.3;">
            <a href="{{ !empty($item['slug']) ? route('shop.show', $item['slug']) : '#products' }}" class="text-decoration-none text-dark stretched-link">
                {{ Str::limit($item['name'] ?? '', 50) }}
            </a>
        </h6>
        <div class="mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-primary fs-5">
                    TSh {{ number_format($item['selling_price'] ?? $item['price'] ?? 0, 0) }}
                </strong>
                @if(isset($item['quantity']))
                    <small class="{{ $item['quantity'] > 0 ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-{{ $item['quantity'] > 0 ? 'check-circle' : 'x-circle' }} me-1"></i>
                        {{ $item['quantity'] > 0 ? 'In Stock' : 'Out of Stock' }}
                    </small>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ !empty($item['slug']) ? route('shop.show', $item['slug']) : '#products' }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                    <i class="bi bi-eye me-1"></i> View Details
                </a>
                <a href="https://wa.me/255000000000?text=I'm%20interested%20in%20{{ urlencode($item['name'] ?? '') }}" target="_blank" class="btn btn-success btn-sm">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</div>
