@props(['item'])

<article class="market-card testimonial-card h-100">
    <div class="stars">
        <i class="bi bi-star-fill"></i>
        <i class="bi bi-star-fill"></i>
        <i class="bi bi-star-fill"></i>
        <i class="bi bi-star-fill"></i>
        <i class="bi bi-star-fill"></i>
    </div>
    <p>"{{ $item['message'] }}"</p>
    <div class="testimonial-author">
        <strong>{{ $item['name'] }}</strong>
        <span>{{ $item['role'] }}</span>
    </div>
</article>
