@props(['item'])

<article class="market-card service-card h-100">
    <div class="market-icon">
        <i class="bi {{ $item['icon'] ?? 'bi-gear' }}"></i>
    </div>
    <span class="market-kicker">{{ $item['category'] ?? 'Service' }}</span>
    <h3>{{ $item['title'] }}</h3>
    <p>{{ $item['description'] }}</p>
    <a href="#booking" class="market-link">Learn More <i class="bi bi-arrow-right"></i></a>
</article>
