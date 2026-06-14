@extends('layouts.app')

@section('title', 'About ' . ($system_settings->system_name ?? 'Chriss Integrated Systems'))

@push('critical-head')
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endpush

@section('content')
    <section class="about-page">
        <div class="container">
            <div class="row g-4 align-items-stretch mb-4">
                <div class="col-lg-7">
                    <div class="about-hero-card h-100">
                        <span class="about-kicker"><i class="bi bi-stars"></i> About {{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</span>
                        <h1 class="about-title">A modern multi-service ERP platform for business operations and customer services.</h1>
                        <p class="about-copy">
                            {{ $system_settings->system_name ?? 'Chriss Integrated Systems' }} integrates product inventory, service management,
                            staff assignments, income tracking, expense management, and customer self-service into one unified platform.
                        </p>
                        <ul class="about-list">
                            <li><i class="bi bi-check-circle-fill"></i><span>Production-friendly structure with multi-role dashboards and reusable layouts.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>Authentication flows for Super Admin, Admin, Staff, and Customer roles.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>A maintainable base for adding features, APIs, and domain-specific modules.</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="about-stat-grid">
                        <div class="about-stat-card">
                            <i class="bi bi-puzzle"></i>
                            <strong>Multi-Role</strong>
                            <span>Super Admin, Admin, Staff & Customer roles</span>
                        </div>
                        <div class="about-stat-card">
                            <i class="bi bi-box-seam"></i>
                            <strong>Inventory</strong>
                            <span>Product management with stock tracking</span>
                        </div>
                        <div class="about-stat-card">
                            <i class="bi bi-gear"></i>
                            <strong>Services</strong>
                            <span>Service catalog with request system</span>
                        </div>
                        <div class="about-stat-card">
                            <i class="bi bi-graph-up"></i>
                            <strong>Finance</strong>
                            <span>Income, expenses & profit reporting</span>
                        </div>
                        <div class="about-stat-card">
                            <i class="bi bi-shield-check"></i>
                            <strong>Secure</strong>
                            <span>Role-based access & activity logging</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h2 class="about-section-title mb-0">Core Platform Features</h2>
                <a href="{{ route('home') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-arrow-left me-1"></i>Back to Home
                </a>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <article class="about-value-card">
                        <span class="about-value-icon"><i class="bi bi-diagram-3"></i></span>
                        <h3>Structured Architecture</h3>
                        <p>Routes, layouts, controllers, and dashboard modules are organized for clarity and scale.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="about-value-card">
                        <span class="about-value-icon"><i class="bi bi-shield-lock"></i></span>
                        <h3>Security First</h3>
                        <p>Role-based middleware and activity logging help you build safer business applications.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="about-value-card">
                        <span class="about-value-icon"><i class="bi bi-lightning-charge"></i></span>
                        <h3>Dynamic Marketplace</h3>
                        <p>Products and services fetched live from the database with eBay-style search and filtering.</p>
                    </article>
                </div>
            </div>

            <div class="row g-3 mt-1 align-items-stretch">
                <div class="col-lg-7">
                    <article class="about-stack-card">
                        <h2 class="about-section-title">Languages & Technologies</h2>
                        <div class="about-stack-grid">
                            <div class="about-stack-item"><i class="bi bi-filetype-php"></i><span>PHP</span></div>
                            <div class="about-stack-item"><i class="bi bi-bootstrap-fill"></i><span>Bootstrap</span></div>
                            <div class="about-stack-item"><i class="bi bi-filetype-js"></i><span>JavaScript</span></div>
                            <div class="about-stack-item"><i class="bi bi-database-fill"></i><span>MySQL</span></div>
                            <div class="about-stack-item"><i class="bi bi-layers-fill"></i><span>Blade</span></div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-5">
                    <article class="about-developer-card">
                        <h2 class="about-section-title">About the Developer</h2>
                        <p>
                            Built by <strong>Zachriss</strong>, a developer focused on practical problem-solving and clean, 
                            scalable architecture. This ERP platform is designed to simplify business operations by providing 
                            a structured, readable, and production-ready foundation that helps organizations manage products, 
                            services, staff, finances, and customer relationships.
                        </p>
                        <p>
                            Maintained with a strong emphasis on clarity, consistent UI, and developer experience, it promotes 
                            best practices while remaining easy to understand, extend, and adapt to real-world business use cases.
                        </p>
                        <div class="about-developer-meta">
                            <span>Laravel</span>
                            <span>UI/UX</span>
                            <span>Clean Code</span>
                            <span>ERP Systems</span>
                            <span>API Development</span>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection