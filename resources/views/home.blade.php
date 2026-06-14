@extends('layouts.app')
@section('title', ($system_settings->system_name ?? 'Chriss Integrated Systems') . ' - Marketplace')
@section('hide_default_footer', '1')

@push('critical-head')
<style>
:root {
    --etsy-primary: {{ $system_settings->primary_color ?? '#3b82f6' }};
    --etsy-secondary: {{ $system_settings->secondary_color ?? '#6c757d' }};
    --etsy-accent: {{ $system_settings->accent_color ?? '#0d6efd' }};
    --etsy-bg: #f8f9fa;
    --etsy-card-shadow: 0 1px 4px rgba(0,0,0,0.06);
    --etsy-card-hover-shadow: 0 6px 20px rgba(0,0,0,0.08);
    --etsy-border: #e8e8e8;
    --etsy-text: #222;
    --etsy-text-muted: #757575;
    --etsy-font: "Graphik", "Arial", "Helvetica", sans-serif;
}
body{background:var(--etsy-bg);font-family:var(--etsy-font)}
.etsy-section{padding:32px 0}
.etsy-section-title{font-size:1.15rem;font-weight:700;color:#222;margin-bottom:16px;letter-spacing:-0.01em}
.etsy-section-title a{font-size:.82rem;font-weight:500;text-decoration:none;color:var(--etsy-primary)}
.etsy-section-title a:hover{text-decoration:underline}

/* ── Etsy-style hero (light, inviting) ── */
.etsy-hero{background:#fff;border-bottom:1px solid var(--etsy-border);padding:24px 0}
.etsy-hero h1{font-size:1.5rem;font-weight:700;color:#222;margin-bottom:4px;letter-spacing:-0.02em}
.etsy-hero p{color:var(--etsy-text-muted);font-size:.92rem;margin-bottom:0}
.etsy-hero .hero-search-wrap{background:#f0f0f0;border-radius:24px;padding:10px 16px;display:flex;align-items:center;gap:8px;max-width:480px;margin-top:10px}
.etsy-hero .hero-search-wrap input{background:transparent;border:0;outline:0;flex:1;font-size:.88rem}
.etsy-hero .hero-search-wrap input::placeholder{color:#aaa}
.etsy-hero .hero-search-wrap button{background:var(--etsy-primary);color:#fff;border:0;border-radius:20px;padding:6px 18px;font-size:.8rem;font-weight:600}

/* ── Trending / category chips ── */
.etsy-trending{padding:12px 0;background:#fff;border-bottom:1px solid var(--etsy-border);overflow-x:auto;white-space:nowrap;scrollbar-width:none}
.etsy-trending::-webkit-scrollbar{display:none}
.etsy-trending .container{display:flex;gap:10px;align-items:center}
.etsy-trending .trend-label{font-size:.74rem;font-weight:700;color:var(--etsy-text-muted);text-transform:uppercase;letter-spacing:.03em;flex-shrink:0}
.etsy-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border:1px solid var(--etsy-border);border-radius:20px;background:#fff;font-size:.78rem;font-weight:500;color:#555;text-decoration:none;transition:all .15s;flex-shrink:0}
.etsy-chip:hover{background:var(--etsy-primary);color:#fff;border-color:var(--etsy-primary);text-decoration:none}

/* ── Etsy product grid ── */
.etsy-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px}
@media(max-width:991px){.etsy-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:575px){.etsy-grid{grid-template-columns:repeat(2,1fr);gap:10px}}

/* ── Etsy product card ── */
.etsy-card{background:#fff;border-radius:4px;overflow:hidden;position:relative;display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:box-shadow .2s}
.etsy-card:hover{box-shadow:var(--etsy-card-hover-shadow);text-decoration:none;color:inherit}
.etsy-card-img-wrap{position:relative;overflow:hidden;background:#f5f5f5;aspect-ratio:1/1}
.etsy-card-img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s}
.etsy-card:hover .etsy-card-img{transform:scale(1.04)}
.etsy-card-badge{position:absolute;top:8px;left:8px;font-size:.62rem;font-weight:700;text-transform:uppercase;padding:4px 8px;border-radius:2px;z-index:2;letter-spacing:.02em}
.etsy-card-body{padding:8px 0 4px}
.etsy-card-shop{font-size:.7rem;color:var(--etsy-text-muted);margin-bottom:1px;font-weight:400}
.etsy-card-title{font-size:.8rem;font-weight:400;color:#222;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:2px}
.etsy-card-price{font-size:.92rem;font-weight:700;color:#222}
.etsy-card-price small{font-size:.68rem;font-weight:400;color:var(--etsy-text-muted);text-decoration:line-through;margin-left:4px}
.etsy-card-rating{font-size:.68rem;color:var(--etsy-text-muted);display:flex;align-items:center;gap:3px;margin-top:1px}
.etsy-card-rating .stars{color:#f5a623;letter-spacing:1px}
.etsy-card-hover-actions{position:absolute;bottom:8px;left:8px;right:8px;opacity:0;transition:opacity .2s;z-index:3;display:flex;gap:4px}
.etsy-card:hover .etsy-card-hover-actions{opacity:1}
.etsy-card-hover-actions .btn{font-size:.7rem;font-weight:600;padding:4px 10px;border-radius:2px;flex:1}

/* ── Booking section (Etsy clean style) ── */
.etsy-booking{background:#fff;border:1px solid var(--etsy-border);border-radius:4px;padding:28px}
.etsy-booking h2{font-size:1.1rem;font-weight:700;color:#222}
.etsy-booking .form-control,.etsy-booking .form-select{border-radius:2px;border:1px solid var(--etsy-border);font-size:.88rem;padding:10px 12px}
.etsy-booking .form-control:focus,.etsy-booking .form-select:focus{border-color:var(--etsy-primary);box-shadow:none}

/* ── Why choose us ── */
.etsy-features{background:#fff;border-top:1px solid var(--etsy-border);border-bottom:1px solid var(--etsy-border);padding:28px 0}
.etsy-feature-item{text-align:center;padding:12px}
.etsy-feature-item i{font-size:1.6rem;color:var(--etsy-primary);margin-bottom:6px;display:block}
.etsy-feature-item strong{font-size:.82rem;color:#222;display:block;margin-bottom:4px}
.etsy-feature-item p{font-size:.76rem;color:var(--etsy-text-muted);margin:0}

/* ── Testimonials ── */
.etsy-testimonial{border:1px solid var(--etsy-border);padding:16px;background:#fff;border-radius:4px;height:100%}
.etsy-testimonial .stars{color:#f5a623;font-size:.8rem}
.etsy-testimonial p{font-size:.8rem;color:#555;line-height:1.5;margin-bottom:8px}
.etsy-testimonial .author{font-size:.78rem;font-weight:600;color:#222}
.etsy-testimonial .role{font-size:.7rem;color:var(--etsy-text-muted)}

/* ── Footer ── */
.etsy-footer{background:#232347;color:rgba(255,255,255,.8);padding:40px 0 16px}
.etsy-footer h4{color:#fff;font-size:.88rem;font-weight:700;margin-bottom:12px;letter-spacing:.02em}
.etsy-footer a{color:rgba(255,255,255,.7);text-decoration:none;font-size:.8rem;display:block;margin-bottom:6px;transition:color .15s}
.etsy-footer a:hover{color:#fff}

/* ── Live search results ── */
.etsy-sr{position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--etsy-border);border-radius:4px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:200;max-height:380px;overflow-y:auto;margin-top:4px}
.etsy-sr-item{display:flex;align-items:center;gap:10px;padding:10px 14px;color:#222;text-decoration:none;border-bottom:1px solid #f0f0f0}
.etsy-sr-item:hover{background:#f8f8f8;text-decoration:none;color:#222}
.etsy-sr-item img{width:40px;height:40px;border-radius:2px;object-fit:cover;background:#f0f0f0;flex-shrink:0}
.etsy-sr-item .sr-name{font-size:.8rem;font-weight:500;color:#222}
.etsy-sr-item .sr-meta{font-size:.7rem;color:var(--etsy-text-muted)}
</style>
@endpush

@section('content')
{{-- ============================================================
     HERO — Etsy light style with search + featured carousel
     ============================================================ --}}
<section class="etsy-hero">
    <div class="container">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-0 px-2 py-1" style="background:var(--etsy-primary);color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.03em">MARKETPLACE</span>
                </div>
                <h1>{{ $settings->hero_title ?? 'Find what you need — products, services & more' }}</h1>
                <p>{{ $settings->hero_subtitle ?? 'Browse unique items from local inventory and book services online.' }}</p>
                <div class="hero-search-wrap">
                    <i class="bi bi-search" style="color:#999;font-size:.85rem"></i>
                    <input type="text" id="heroSearch" placeholder="Search products & services..." autocomplete="off">
                    <button type="button" onclick="window.location='{{ route('shop.index') }}?search='+encodeURIComponent(document.getElementById('heroSearch').value)"><i class="bi bi-arrow-right d-md-none"></i><span class="d-none d-md-inline">Search</span></button>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="button" onclick="openServiceModal()" class="btn shadow-sm d-inline-flex align-items-center gap-2 fw-bold" style="background:var(--etsy-primary);color:#fff;border:0;border-radius:20px;padding:8px 18px;font-size:.82rem">
                        <i class="bi bi-wrench-adjustable-circle"></i> Book a Service
                    </button>
                </div>
                <div id="heroSearchResults" class="etsy-sr d-none"></div>
            </div>
            <div class="col-lg-7">
                <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3500">
                    <div class="carousel-inner rounded-0 overflow-hidden" style="aspect-ratio:16/9">
                        @php $heroSlides = collect(); @endphp
                        @foreach($featuredProducts as $p)
                            @php $heroSlides->push(['type'=>'product','url'=>route('shop.show',$p->slug??$p->id),'image'=>$p->image_url,'name'=>$p->name,'price'=>$p->selling_price,'badge'=>'Product']); @endphp
                        @endforeach
                        @foreach($homepageServices as $sv)
                            @php $heroSlides->push(['type'=>'service','url'=>route('services.show',$sv->slug),'image'=>$sv->featured_image_url,'name'=>$sv->name,'price'=>$sv->base_price,'badge'=>'Service']); @endphp
                        @endforeach
                        @if($latestProducts->isNotEmpty())
                            @foreach($latestProducts as $p)
                                @php $heroSlides->push(['type'=>'product','url'=>route('shop.show',$p->slug??$p->id),'image'=>$p->image_url,'name'=>$p->name,'price'=>$p->selling_price,'badge'=>'Product']); @endphp
                            @endforeach
                        @endif
                        @if($heroSlides->isNotEmpty())
                            @foreach($heroSlides->shuffle() as $i => $slide)
                                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                    <a href="{{ $slide['url'] }}" class="d-block position-relative text-decoration-none">
                                        <img src="{{ $slide['image'] }}" class="d-block w-100" alt="{{ $slide['name'] }}" style="height:100%;width:100%;object-fit:cover"
                                             onerror="this.src='https://placehold.co/800x450/f0f0f0/bbb?text={{ $slide['badge'] }}'">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background:linear-gradient(transparent,rgba(0,0,0,.6))">
                                            <strong class="text-white d-block" style="font-size:.9rem">{{ $slide['name'] }}</strong>
                                            <span class="text-white-50" style="font-size:.78rem">{{ $slide['price'] > 0 ? 'TZS '.number_format($slide['price'],0) : 'Contact for price' }}</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height:100%">
                                    <div class="text-center text-muted"><i class="bi bi-shop display-3"></i><p class="mt-2 fw-bold">Shop Now</p></div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" style="width:32px;height:32px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.9);border:1px solid #ddd">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="filter:invert(.6);width:12px;height:12px"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" style="width:32px;height:32px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.9);border:1px solid #ddd">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="filter:invert(.6);width:12px;height:12px"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     TRENDING CATEGORIES — Etsy-style chip row
     ============================================================ --}}
<div class="etsy-trending">
    <div class="container">
        <span class="trend-label">Trending</span>
        @foreach($serviceCategories->take(8) as $cat)
            <a href="{{ route('services.index', ['category' => $cat->slug]) }}" class="etsy-chip">{{ $cat->name }}</a>
        @endforeach
        <a href="{{ route('shop.index') }}" class="etsy-chip"><i class="bi bi-grid-3x3-gap"></i> Shop All</a>
    </div>
</div>

{{-- ============================================================
     FEATURED PRODUCTS — Etsy listing grid
     ============================================================ --}}
<section class="etsy-section" id="products">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center etsy-section-title">
            <span>Featured Products</span>
            <a href="{{ route('shop.index') }}">View all <i class="bi bi-chevron-right" style="font-size:.7rem"></i></a>
        </div>
        @if($featuredProducts->isEmpty())
            <p class="text-muted small">No featured products yet. Check back soon!</p>
        @else
            <div class="etsy-grid">
                @foreach($featuredProducts as $p)
                <a href="{{ route('shop.show', $p->slug ?? $p->id) }}" class="etsy-card">
                    <div class="etsy-card-img-wrap">
                        <img src="{{ $p->image_url }}" class="etsy-card-img" alt="{{ $p->name }}" onerror="this.src='https://placehold.co/400x400/f5f5f5/ccc?text=Product'">
                        <span class="etsy-card-badge" style="background:var(--etsy-primary);color:#fff">Featured</span>
                        <div class="etsy-card-hover-actions">
                            <span class="btn btn-light btn-sm shadow-sm" style="background:#fff;border:1px solid #ddd;border-radius:2px;text-align:center">Quick view</span>
                        </div>
                    </div>
                    <div class="etsy-card-body">
                        @if($p->category)<div class="etsy-card-shop">{{ $p->category->name }}</div>@endif
                        <div class="etsy-card-title">{{ $p->name }}</div>
                        <div class="etsy-card-price">TZS {{ number_format($p->selling_price,0) }}</div>
                        <div class="etsy-card-rating">
                            <span class="stars">★★★★★</span>
                            <span class="badge rounded-0 px-1 py-0" style="background:{{ $p->quantity>0 ? '#10b981' : '#ef4444' }};color:#fff;font-size:.6rem;font-weight:600">{{ $p->quantity>0 ? 'In Stock' : 'Out of Stock' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============================================================
     FEATURED SERVICES — Etsy style
     ============================================================ --}}
<section class="etsy-section" style="background:#fff;border-top:1px solid var(--etsy-border);border-bottom:1px solid var(--etsy-border)" id="services">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center etsy-section-title">
            <span>Featured Services</span>
            <a href="{{ route('services.index') }}">View all <i class="bi bi-chevron-right" style="font-size:.7rem"></i></a>
        </div>
        @if($homepageServices->isEmpty())
            <p class="text-muted small">No services available yet.</p>
        @else
            <div class="etsy-grid">
                @foreach($homepageServices as $sv)
                <a href="{{ route('services.show', $sv->slug) }}" class="etsy-card">
                    <div class="etsy-card-img-wrap">
                        <img src="{{ $sv->featured_image_url }}" class="etsy-card-img" alt="{{ $sv->name }}" style="object-position:center" onerror="this.src='https://placehold.co/400x400/f5f5f5/ccc?text=Service'">
                        <div class="etsy-card-hover-actions">
                            <span class="btn btn-light btn-sm shadow-sm" style="background:#fff;border:1px solid #ddd;border-radius:2px;text-align:center">Book now</span>
                        </div>
                    </div>
                    <div class="etsy-card-body">
                        @if($sv->category)<div class="etsy-card-shop">{{ $sv->category->name }}</div>@endif
                        <div class="etsy-card-title">{{ $sv->name }}</div>
                        <div class="etsy-card-price">
                            TZS {{ number_format($sv->base_price??0,0) }}
                            @if($sv->duration_hours)<small>{{ $sv->duration_hours }}h</small>@endif
                        </div>
                        <div class="etsy-card-rating">
                            <span class="stars">★★★★★</span>
                            <span style="font-size:.68rem">Book online</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============================================================
     LATEST PRODUCTS
     ============================================================ --}}
<section class="etsy-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center etsy-section-title">
            <span>Latest Products</span>
            <a href="{{ route('shop.index') }}">Shop all <i class="bi bi-chevron-right" style="font-size:.7rem"></i></a>
        </div>
        @if($latestProducts->isEmpty())
            <p class="text-muted small">No products yet.</p>
        @else
            <div class="etsy-grid">
                @foreach($latestProducts as $p)
                <a href="{{ route('shop.show', $p->slug ?? $p->id) }}" class="etsy-card">
                    <div class="etsy-card-img-wrap">
                        <img src="{{ $p->image_url }}" class="etsy-card-img" alt="{{ $p->name }}" onerror="this.src='https://placehold.co/400x400/f5f5f5/ccc?text=Product'">
                        <div class="etsy-card-hover-actions">
                            <span class="btn btn-light btn-sm shadow-sm" style="background:#fff;border:1px solid #ddd;border-radius:2px;text-align:center">Quick view</span>
                        </div>
                    </div>
                    <div class="etsy-card-body">
                        @if($p->category)<div class="etsy-card-shop">{{ $p->category->name }}</div>@endif
                        <div class="etsy-card-title">{{ $p->name }}</div>
                        <div class="etsy-card-price">TZS {{ number_format($p->selling_price,0) }}</div>
                        <div class="etsy-card-rating">
                            <span class="stars">★★★★★</span>
                            <span class="badge rounded-0 px-1 py-0" style="background:{{ $p->quantity>0 ? '#10b981' : '#ef4444' }};color:#fff;font-size:.6rem;font-weight:600">{{ $p->quantity>0 ? 'In Stock' : 'Out of Stock' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============================================================
     SERVICE BOOKING MODAL — Popup form
     ============================================================ --}}
<div id="serviceBookingModal" class="modal-overlay" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);overflow-y:auto;padding:20px" onclick="closeServiceModal(event)">
    <div style="max-width:800px;margin:60px auto;background:#fff;border-radius:8px;padding:28px;position:relative" onclick="event.stopPropagation()">
        <button type="button" onclick="closeServiceModal()" style="position:absolute;top:12px;right:16px;border:0;background:transparent;font-size:1.4rem;color:#999;cursor:pointer;line-height:1">&times;</button>
        <div class="etsy-booking">
            <div class="row g-4">
                <div class="col-lg-5 d-flex flex-column justify-content-center">
                    <span class="badge rounded-0 px-2 py-1 mb-2 d-inline-flex align-items-center gap-1" style="background:var(--etsy-primary);color:#fff;width:fit-content;font-size:.65rem;font-weight:700;letter-spacing:.03em">SERVICE BOOKING</span>
                    <h2 class="mb-2">Tell us what you need</h2>
                    <p class="text-muted small mb-0">Submit a request for repairs, networking, software support, printing, electrical work, or internet services. We'll get back to you within 24 hours.</p>
                    <div class="mt-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill" style="color:var(--etsy-primary);font-size:.8rem"></i>
                            <span class="small text-muted">Free estimate</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill" style="color:var(--etsy-primary);font-size:.8rem"></i>
                            <span class="small text-muted">Licensed professionals</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill" style="color:var(--etsy-primary);font-size:.8rem"></i>
                            <span class="small text-muted">Satisfaction guaranteed</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('public.service-requests.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if (session('success'))<div class="alert alert-success py-2 small">{{ session('success') }}</div>@endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">Service</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="">Select a service...</option>
                                    @foreach($serviceOptions as $sv)<option value="{{ $sv->id }}" @selected(old('service_id')==$sv->id)>{{ $sv->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-medium text-muted">Description</label>
                                <textarea name="problem_description" rows="3" class="form-control" required>{{ old('problem_description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-medium text-muted">Upload Image <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="file" name="problem_image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn w-100 py-2 fw-bold" style="background:var(--etsy-primary);color:#fff;border:0;border-radius:2px;font-size:.88rem"><i class="bi bi-send me-2"></i>Submit Request</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     WHY CHOOSE US — Etsy feature highlights
     ============================================================ --}}
<section class="etsy-features">
    <div class="container">
        <div class="etsy-grid" style="grid-template-columns:repeat(5,1fr)">
            @foreach([
                ['icon'=>'bi-lightning-charge-fill','title'=>'Fast Service','text'=>'Quick response for urgent repairs and support.'],
                ['icon'=>'bi-people-fill','title'=>'Professionals','text'=>'Skilled technicians across software & hardware.'],
                ['icon'=>'bi-tag-fill','title'=>'Affordable','text'=>'Transparent pricing for local customers.'],
                ['icon'=>'bi-chat-dots-fill','title'=>'Support','text'=>'Every request is tracked for better follow-up.'],
                ['icon'=>'bi-shop','title'=>'One Platform','text'=>'Products, repairs, internet & more in one place.']
            ] as $r)
            <div class="etsy-card" style="cursor:default;text-align:center;padding:20px 12px;background:#fff;border-radius:4px;text-decoration:none;color:inherit;transition:box-shadow .2s">
                <div style="text-align:center;padding:8px 0 4px">
                    <i class="bi {{ $r['icon'] }}" style="font-size:1.6rem;color:var(--etsy-primary);margin-bottom:6px;display:block"></i>
                    <strong style="font-size:.82rem;color:#222;display:block;margin-bottom:4px">{{ $r['title'] }}</strong>
                    <p style="font-size:.76rem;color:var(--etsy-text-muted);margin:0">{{ $r['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     ABOUT SECTION (Dynamic) — Circle avatar + numbered items
     ============================================================ --}}
@if($settings->about_description)
@php
// Parse about_description into structured sections
$aboutText = $settings->about_description;
$blocks = preg_split('/\n{2,}/', $aboutText);
$sectionNumber = 0;
function isEmojiLine($line) {
    // Check if line starts with an emoji (or common emoji patterns)
    return preg_match('/^[✔💡🌐🛠️⚡📡🛒📚💳🎬🖨️🏪🎯👁️🤝🖥️]/u', trim($line));
}
@endphp
<section class="etsy-section" id="about-section" style="background:#fff;border-top:1px solid var(--etsy-border);border-bottom:1px solid var(--etsy-border)">
    <div class="container">
        <div class="row g-4 align-items-start">
            @if($settings->about_image)
            <div class="col-lg-5 text-center">
                <img src="{{ asset('storage/' . $settings->about_image) }}" alt="About {{ $system_settings->system_name }}"
                     class="rounded-circle shadow-sm"
                     style="width:280px;height:280px;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                <span class="badge rounded-0 px-2 py-1 mt-3 d-inline-block" style="background:var(--etsy-primary);color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.03em">ABOUT US</span>
                <h2 class="mb-0 mt-1" style="font-size:1.4rem;font-weight:700;color:#222">About {{ $system_settings->system_name ?? 'Us' }}</h2>
            </div>
            <div class="col-lg-7">
            @else
            <div class="col-lg-12">
                <span class="badge rounded-0 px-2 py-1 mb-2" style="background:var(--etsy-primary);color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.03em">ABOUT US</span>
                <h2 class="mb-3" style="font-size:1.4rem;font-weight:700;color:#222">About {{ $system_settings->system_name ?? 'Us' }}</h2>
            @endif
                <div style="font-size:.9rem;color:#555;line-height:1.7">
                    @php $itemCounter = 1; @endphp
                    @foreach($blocks as $block)
                        @php $block = trim($block); @endphp
                        @continue(empty($block))
                        @php
                            $lines = preg_split('/\n+/', $block);
                            $firstLine = trim($lines[0] ?? '');
                        @endphp

                        {{-- Intro paragraph (no emoji, no bullet) --}}
                        @if(!isEmojiLine($firstLine) && !str_starts_with($firstLine, '-') && !str_starts_with($firstLine, '•') && count($lines) <= 2)
                            <p class="mb-3">{{ $firstLine }}</p>

                        {{-- Emoji section header --}}
                        @elseif(isEmojiLine($firstLine))
                            @php $sectionNumber++; @endphp
                            <div class="mb-3">
                                <h5 class="fw-bold mb-2" style="font-size:.95rem;color:#222;">
                                    <span class="d-inline-flex align-items-center justify-content-center me-1"
                                          style="width:26px;height:26px;border-radius:50%;background:var(--etsy-primary);color:#fff;font-size:.75rem;font-weight:700;">
                                        {{ $sectionNumber }}
                                    </span>
                                    {{ preg_replace('/^[✔💡🌐🛠️⚡📡🛒📚💳🎬🖨️🏪🎯👁️🤝🖥️]\s*/u', '', $firstLine) }}
                                </h5>
                                @if(count($lines) > 1)
                                    <ul class="list-unstyled ms-4 mb-0" style="font-size:.88rem;">
                                        @foreach(array_slice($lines, 1) as $subLine)
                                            @php $subLine = trim($subLine); @endphp
                                            @continue(empty($subLine))
                                            @if(str_starts_with($subLine, '-') || str_starts_with($subLine, '•'))
                                                <li class="mb-1 d-flex align-items-baseline gap-2">
                                                    <span style="color:var(--etsy-primary);font-size:.6rem;">●</span>
                                                    <span>{{ ltrim($subLine, '- •') }}</span>
                                                </li>
                                            @else
                                                <li class="mb-1 d-flex align-items-baseline gap-2">
                                                    <span style="color:var(--etsy-primary);font-size:.6rem;">●</span>
                                                    <span>{{ $subLine }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                        {{-- Regular bullet list or text block --}}
                        @else
                            <div class="mb-3">
                                @foreach($lines as $line)
                                    @php $line = trim($line); @endphp
                                    @continue(empty($line))
                                    @if(str_starts_with($line, '-') || str_starts_with($line, '•'))
                                        <div class="d-flex align-items-baseline gap-2 ms-4 mb-1">
                                            <span style="color:var(--etsy-primary);font-size:.6rem;">●</span>
                                            <span>{{ ltrim($line, '- •') }}</span>
                                        </div>
                                    @else
                                        <p class="mb-1">{{ $line }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     TESTIMONIALS (Dynamic from Database)
     ============================================================ --}}
@if($testimonials->isNotEmpty())
<section class="etsy-section">
    <div class="container">
        <div class="etsy-section-title text-center mb-3">
            <span style="font-size:1.1rem">What our customers say</span>
        </div>
        <div class="etsy-grid">
            @foreach($testimonials as $t)
            <div class="etsy-card" style="cursor:default;padding:16px">
                <div class="etsy-card-body">
                    <div class="stars mb-2" style="color:#f5a623;font-size:.8rem;letter-spacing:1px">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
                    <p style="font-size:.8rem;color:#555;line-height:1.5;margin-bottom:8px">"{{ $t->message }}"</p>
                    <div style="font-size:.78rem;font-weight:600;color:#222">{{ $t->name }}</div>
                    @if($t->role)<div style="font-size:.7rem;color:var(--etsy-text-muted)">{{ $t->role }}</div>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     CONTACT SECTION (Dynamic + Contact Form)
     ============================================================ --}}
<section class="etsy-section" style="background:#fff;border-top:1px solid var(--etsy-border)">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <span class="badge rounded-0 px-2 py-1 mb-2" style="background:var(--etsy-primary);color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.03em">GET IN TOUCH</span>
                <h2 class="mb-3" style="font-size:1.3rem;font-weight:700;color:#222">Contact Us</h2>
                <p class="text-muted small mb-3">Have a question or want to work with us? Send us a message.</p>
                <div class="mb-3">
                    <p style="font-size:.85rem;margin-bottom:8px"><i class="bi bi-geo-alt me-2" style="color:var(--etsy-primary)"></i>{{ $settings->contact_address ?? $system_settings->address ?? 'Tanzania' }}</p>
                    <p style="font-size:.85rem;margin-bottom:8px"><i class="bi bi-telephone me-2" style="color:var(--etsy-primary)"></i>{{ $settings->contact_phone ?? $system_settings->phone ?? '+255' }}</p>
                    <p style="font-size:.85rem;margin-bottom:8px"><i class="bi bi-envelope me-2" style="color:var(--etsy-primary)"></i>{{ $settings->contact_email ?? $system_settings->email ?? 'info@example.com' }}</p>
                </div>
                @if($socialLinks->isNotEmpty())
                <div class="d-flex gap-2 mt-3">
                    @foreach($socialLinks as $sl)
                    <a href="{{ $sl['url'] }}" target="_blank" class="d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;background:var(--etsy-primary);color:#fff;font-size:.85rem;text-decoration:none" title="{{ $sl['name'] }}">
                        <i class="bi {{ $sl['icon'] }}"></i>
                    </a>
                    @endforeach
                </div>
                @endif
                @if($settings->map_embed_url)
                <div class="mt-3">
                    <div style="border-radius:8px;overflow:hidden;max-height:200px">
                        {!! $settings->map_embed_url !!}
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-7">
                <form method="POST" action="{{ route('public.contact.store') }}">
                    @csrf
                    @if(session('success'))<div class="alert alert-success py-2 small">{{ session('success') }}</div>@endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required style="border-radius:4px;font-size:.88rem">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required style="border-radius:4px;font-size:.88rem">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" style="border-radius:4px;font-size:.88rem">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" style="border-radius:4px;font-size:.88rem">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium text-muted">Message <span class="text-danger">*</span></label>
                            <textarea name="message" rows="4" class="form-control" required style="border-radius:4px;font-size:.88rem">{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn py-2 fw-bold px-4" style="background:var(--etsy-primary);color:#fff;border:0;border-radius:4px;font-size:.88rem"><i class="bi bi-send me-2"></i>Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     FOOTER — Etsy-inspired dark footer (fully dynamic)
     ============================================================ --}}
<footer class="etsy-footer" id="contact">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h4>{{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</h4>
                <p style="font-size:.8rem;color:rgba(255,255,255,.6)">{{ $settings->system_description ?? 'Commercial technology platform for products, services, internet packages, repairs, and ERP-backed operations.' }}</p>
                @if($socialLinks->isNotEmpty())
                <div class="d-flex gap-2 mt-3">
                    @foreach($socialLinks as $sl)
                    <a href="{{ $sl['url'] }}" target="_blank" style="display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.2);border-radius:50%;font-size:.8rem;color:rgba(255,255,255,.8);text-decoration:none" title="{{ $sl['name'] }}">
                        <i class="bi {{ $sl['icon'] }}"></i>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="col-sm-6 col-lg-2">
                <h4>Quick Links</h4>
                @forelse($quickLinks as $ql)
                    <a href="{{ $ql->url ?: '#' }}">{{ $ql->name }}</a>
                @empty
                    <a href="{{ route('shop.index') }}">Shop</a>
                    <a href="{{ route('services.index') }}">Services</a>
                    <a href="{{ url('/') }}#about-section">About</a>
                @endforelse
            </div>
            <div class="col-sm-6 col-lg-3">
                <h4>Services</h4>
                @forelse($serviceLinks as $sl)
                    <a href="{{ $sl->url ?: '#' }}">{{ $sl->name }}</a>
                @empty
                    @foreach($homepageServices->take(5) as $sv)
                        <a href="{{ route('services.show', $sv->slug) }}">{{ $sv->name }}</a>
                    @endforeach
                @endforelse
            </div>
            <div class="col-lg-3">
                <h4>Contact</h4>
                <p style="font-size:.8rem;margin-bottom:8px"><i class="bi bi-geo-alt me-2"></i>{{ $settings->contact_address ?? $system_settings->address ?? 'Tanzania' }}</p>
                <p style="font-size:.8rem;margin-bottom:8px"><i class="bi bi-telephone me-2"></i>{{ $settings->contact_phone ?? $system_settings->phone ?? '+255' }}</p>
                <p style="font-size:.8rem;margin-bottom:0"><i class="bi bi-envelope me-2"></i>{{ $settings->contact_email ?? $system_settings->email ?? 'info@example.com' }}</p>
            </div>
        </div>
        <div class="text-center pt-3 mt-3 small" style="border-top:1px solid rgba(255,255,255,.1);font-size:.76rem;color:rgba(255,255,255,.5)">
            &copy; {{ date('Y') }} {{ $system_settings->system_name ?? 'CIS' }}. {{ $system_settings->footer_text ?? 'All rights reserved.' }}
        </div>
    </div>
</footer>
@endsection

@push('scripts')
<script>
// ── Service Booking Modal ──
function openServiceModal() {
    document.getElementById('serviceBookingModal').style.display = '';
    document.body.style.overflow = 'hidden';
}
function closeServiceModal(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('serviceBookingModal').style.display = 'none';
    document.body.style.overflow = '';
}
// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('serviceBookingModal');
        if (modal && modal.style.display !== 'none') {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
});

// AJAX live search on homepage hero
const hSI = document.getElementById('heroSearch');
const hSR = document.getElementById('heroSearchResults');
let hTimer;
if (hSI) {
    hSI.addEventListener('input', function(){
        clearTimeout(hTimer);
        const q = this.value.trim();
        if(q.length < 2){ hSR.classList.add('d-none'); return; }
        hTimer = setTimeout(() => {
            fetch('{{ route("marketplace.search") }}?q=' + encodeURIComponent(q))
            .then(r => r.json()).then(d => {
                hSR.classList.remove('d-none');
                if(!d.results || d.results.length === 0){
                    hSR.innerHTML = '<div class="p-3 small text-muted">No results. Try a different search.</div>';
                    return;
                }
                let html = '';
                d.results.forEach(r => {
                    html += `<a href="${r.url}" class="etsy-sr-item">
                        ${r.image ? `<img src="${r.image}" alt="${r.name}" onerror="this.style.display='none'">` : `<div style="width:40px;height:40px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-${r.type === 'product' ? 'box-seam' : 'gear'} text-muted"></i></div>`}
                        <div class="flex-grow-1">
                            <div class="sr-name">${r.name}</div>
                            <div class="sr-meta">${r.type} · TZS ${r.price?.toLocaleString() || 'N/A'}</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted" style="font-size:.7rem"></i>
                    </a>`;
                });
                hSR.innerHTML = html;
            });
        }, 300);
    });
    document.addEventListener('click', e => {
        if(!e.target.closest('#heroSearch') && !e.target.closest('#heroSearchResults') && !e.target.closest('button[onclick]')) {
            hSR.classList.add('d-none');
        }
    });
}
</script>
@endpush