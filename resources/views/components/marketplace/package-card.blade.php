@props(['item', 'featured' => false])

<article class="market-card package-card h-100 {{ $featured ? 'featured-package' : '' }}">
    @if($featured)
        <span class="package-badge">Popular</span>
    @endif
    <div class="market-icon">
        <i class="bi bi-wifi"></i>
    </div>
    <h3>{{ $item['name'] }}</h3>
    <p>{{ $item['duration'] }}</p>
    <div class="package-price">TZS {{ number_format((float) $item['price']) }}</div>
    <a href="#booking" class="btn {{ $featured ? 'btn-light' : 'btn-primary' }} w-100">Purchase Package</a>
</article>
