@extends('layouts.app')

@section('title', $product->name . ' - Shop')

@push('styles')
<style>
    .product-detail-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    .product-detail-wrapper {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .product-gallery {
        padding: 1.5rem;
        border-right: 1px solid #f0f0f0;
    }
    .product-main-image {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 12px;
        background: #f8f9fa;
        cursor: zoom-in;
        transition: transform 0.3s ease;
    }
    .product-main-image:hover {
        transform: scale(1.02);
    }
    .gallery-thumbs {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
    .gallery-thumb {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.2s;
    }
    .gallery-thumb.active, .gallery-thumb:hover {
        border-color: #667eea;
    }
    .product-info {
        padding: 2rem;
    }
    .product-brand {
        color: #667eea;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .product-name {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .product-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: #f59e0b;
    }
    .product-price-section {
        display: flex;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
    }
    .product-price {
        font-size: 2rem;
        font-weight: 900;
        color: #0d6efd;
    }
    .product-price small {
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748b;
    }
    .product-stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .product-short-desc {
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }
    .product-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .product-meta-item {
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
    }
    .product-meta-item .label {
        font-size: 0.75rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.03em;
    }
    .product-meta-item .value {
        font-weight: 700;
        color: #1e293b;
    }
    .product-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    .product-actions .btn {
        flex: 1;
        padding: 0.75rem;
        font-weight: 700;
        border-radius: 10px;
    }
    .description-section {
        padding: 2rem;
        border-top: 1px solid #f0f0f0;
    }
    .description-section h3 {
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    .description-section .content {
        color: #64748b;
        line-height: 1.8;
    }
    .related-section {
        margin-top: 2rem;
        padding: 1.5rem 0;
    }
    .related-section h3 {
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1.5rem;
    }
    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
    }
    .related-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .related-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }
    .related-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: #f3f4f6;
    }
    .related-card-body {
        padding: 1rem;
    }
    .related-card-body h5 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .related-card-body .price {
        color: #0d6efd;
        font-weight: 800;
        font-size: 1.1rem;
    }
    .breadcrumb-custom {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }
    .breadcrumb-custom a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }
    .breadcrumb-custom a:hover { color: #fff; }
    .breadcrumb-custom .sep { color: rgba(255,255,255,0.4); }
    .breadcrumb-custom .current { color: #fff; font-weight: 600; }
    @media (max-width: 767.98px) {
        .product-gallery { border-right: none; }
        .product-info { padding: 1.25rem; }
        .product-meta-grid { grid-template-columns: 1fr; }
        .product-actions { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="product-detail-hero">
    <div class="container">
        <div class="breadcrumb-custom">
            <a href="{{ route('home') }}">Home</a>
            <span class="sep">/</span>
            <a href="{{ route('shop.index') }}">Shop</a>
            <span class="sep">/</span>
            <span class="current">{{ $product->name }}</span>
        </div>
        <h1 class="h3 mb-0">{{ $product->name }}</h1>
        <p class="mb-0 opacity-75">{{ $product->category?->name ?? 'Product' }}</p>
    </div>
</div>

<div class="container">
    <div class="product-detail-wrapper">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="product-gallery">
                    @php
                        $images = $product->galleries->pluck('image_path')->toArray();
                        $mainImage = $product->image_url;
                    @endphp
                    <img src="{{ $mainImage }}"
                         class="product-main-image"
                         id="main-product-image"
                         alt="{{ $product->name }}"
                         onerror="this.src='https://placehold.co/600x600/e2e8f0/64748b?text=No+Image'">
                    @if(count($images) > 0)
                    <div class="gallery-thumbs">
                        <img src="{{ $mainImage }}"
                             class="gallery-thumb active"
                             onclick="changeImage(this.src)"
                             alt="Main"
                             onerror="this.src='https://placehold.co/600x600/e2e8f0/64748b?text=No+Image'">
                        @foreach($images as $img)
                            <img src="{{ asset('storage/' . $img) }}"
                                 class="gallery-thumb"
                                 onclick="changeImage(this.src)"
                                 alt="Gallery"
                                 onerror="this.src='https://placehold.co/600x600/e2e8f0/64748b?text=No+Image'">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <div class="product-info">
                    @if($product->brand)
                        <div class="product-brand">{{ $product->brand }}</div>
                    @endif
                    <h1 class="product-name">{{ $product->name }}</h1>

                    <div class="product-rating">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                        <span class="text-muted small">({{ rand(8, 50) }} reviews)</span>
                    </div>

                    <div class="product-price-section">
                        <div>
                            <div class="product-price">
                                TSh {{ number_format($product->selling_price, 0) }}
                            </div>
                            @if($product->buying_price > 0 && $product->selling_price > $product->buying_price)
                                <small>Market Price: TSh {{ number_format($product->selling_price * 1.15, 0) }}</small>
                            @endif
                        </div>
                        <div class="ms-auto">
                            @if($product->quantity <= 0)
                                <span class="product-stock-badge bg-danger text-white">
                                    <i class="bi bi-x-circle"></i> Out of Stock
                                </span>
                            @elseif($product->quantity <= $product->low_stock_alert_level)
                                <span class="product-stock-badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-triangle"></i> Only {{ $product->quantity }} left
                                </span>
                            @else
                                <span class="product-stock-badge bg-success text-white">
                                    <i class="bi bi-check-circle"></i> In Stock ({{ $product->quantity }})
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($product->short_description)
                        <p class="product-short-desc">{{ $product->short_description }}</p>
                    @endif

                    <div class="product-meta-grid">
                        <div class="product-meta-item">
                            <div class="label">SKU</div>
                            <div class="value">{{ $product->sku }}</div>
                        </div>
                        <div class="product-meta-item">
                            <div class="label">Category</div>
                            <div class="value">{{ $product->category?->name ?? 'Uncategorized' }}</div>
                        </div>
                        @if($product->barcode)
                        <div class="product-meta-item">
                            <div class="label">Barcode</div>
                            <div class="value">{{ $product->barcode }}</div>
                        </div>
                        @endif
                        <div class="product-meta-item">
                            <div class="label">Stock Status</div>
                            <div class="value">
                                @if($product->quantity > 10)
                                    <span class="text-success">Available</span>
                                @elseif($product->quantity > 0)
                                    <span class="text-warning">Low Stock</span>
                                @else
                                    <span class="text-danger">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="product-actions">
                        <a href="tel:+255000000000" class="btn btn-primary">
                            <i class="bi bi-telephone me-2"></i> Call to Order
                        </a>
                        <a href="https://wa.me/255000000000?text=I'm%20interested%20in%20{{ urlencode($product->name) }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($product->description)
        <div class="description-section">
            <h3><i class="bi bi-info-circle me-2"></i> Product Description</h3>
            <div class="content">{!! nl2br(e($product->description)) !!}</div>
        </div>
        @endif
    </div>

    @if($relatedProducts->count() > 0)
    <div class="related-section">
        <h3><i class="bi bi-link me-2"></i> Related Products</h3>
        <div class="related-grid">
            @foreach($relatedProducts as $related)
            <a href="{{ route('shop.show', $related->slug ?? $related->id) }}" class="text-decoration-none">
                <div class="related-card">
                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}"
                         onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=No+Image'">
                    <div class="related-card-body">
                        <h5>{{ $related->name }}</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">TSh {{ number_format($related->selling_price, 0) }}</span>
                            <span class="small {{ $related->quantity <= 0 ? 'text-danger' : 'text-success' }}">
                                {{ $related->quantity <= 0 ? 'Out of Stock' : 'In Stock' }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function changeImage(src) {
    document.getElementById('main-product-image').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
@endpush