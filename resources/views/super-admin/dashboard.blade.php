@extends('super-admin.layouts.super-admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .sa-stat-icon.custom-bg-1 { background: #eef2ff; color: #4f46e5; }
    .sa-stat-icon.custom-bg-2 { background: #fef2f2; color: #ef4444; }
    .sa-stat-icon.custom-bg-3 { background: #f0fdf4; color: #16a34a; }
    .sa-stat-icon.custom-bg-4 { background: #fff7ed; color: #f97316; }
    .sa-stat-icon.custom-bg-5 { background: #fdf2f8; color: #ec4899; }
    .sa-stat-icon.custom-bg-6 { background: #f0f9ff; color: #0ea5e9; }
    .sa-stat-icon.custom-bg-7 { background: #f5f3ff; color: #8b5cf6; }
    .sa-stat-icon.custom-bg-8 { background: #fefce8; color: #eab308; }
    .chart-container { position: relative; height: 220px; width: 100%; }
    .chart-container-sm { position: relative; height: 180px; width: 100%; }
</style>
@endpush

@section('content')
<div class="sa-page-header">
    <h1>Super Admin Dashboard</h1>
    <p>Control users, roles, services, inventory, content and system-wide reports.</p>
</div>

{{-- ============================================================
     STATS ROW 1 — Users & System
     ============================================================ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Total Users</p>
            <div class="sa-stat-icon custom-bg-1"><i class="bi bi-people-fill"></i></div>
            <div class="sa-stat-value">{{ $totalUsers }}</div>
            <small>registered accounts</small>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Active Users</p>
            <div class="sa-stat-icon custom-bg-2"><i class="bi bi-person-check-fill"></i></div>
            <div class="sa-stat-value">{{ $activeUsers }}</div>
            <small>currently enabled</small>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Total Roles</p>
            <div class="sa-stat-icon custom-bg-3"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="sa-stat-value">{{ $totalRoles }}</div>
            <small>permission groups</small>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">System Logs</p>
            <div class="sa-stat-icon custom-bg-4"><i class="bi bi-clock-history"></i></div>
            <div class="sa-stat-value">{{ $auditLogsCount }}</div>
            <small>activity records</small>
        </div>
    </div>
</div>

{{-- ============================================================
     STATS ROW 2 — Inventory + Services
     ============================================================ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="sa-card sa-stat" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <p class="sa-stat-label" style="color: rgba(255,255,255,0.8);">Total Products</p>
            <div class="sa-stat-icon" style="color: rgba(255,255,255,0.5);"><i class="bi bi-box-seam-fill"></i></div>
            <div class="sa-stat-value" style="color: #fff;">{{ $totalProducts }}</div>
            <small style="color: rgba(255,255,255,0.7);">in inventory</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="sa-card sa-stat" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <p class="sa-stat-label" style="color: rgba(255,255,255,0.8);">Low Stock</p>
            <div class="sa-stat-icon" style="color: rgba(255,255,255,0.5);"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="sa-stat-value" style="color: #fff;">{{ $lowStockProducts }}</div>
            <small style="color: rgba(255,255,255,0.7);">need restock</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="sa-card sa-stat" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <p class="sa-stat-label" style="color: rgba(255,255,255,0.8);">Featured</p>
            <div class="sa-stat-icon" style="color: rgba(255,255,255,0.5);"><i class="bi bi-star-fill"></i></div>
            <div class="sa-stat-value" style="color: #fff;">{{ $featuredProducts }}</div>
            <small style="color: rgba(255,255,255,0.7);">featured products</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="sa-card sa-stat" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <p class="sa-stat-label" style="color: rgba(255,255,255,0.8);">Services</p>
            <div class="sa-stat-icon" style="color: rgba(255,255,255,0.5);"><i class="bi bi-gear-fill"></i></div>
            <div class="sa-stat-value" style="color: #fff;">{{ $totalServices }}</div>
            <small style="color: rgba(255,255,255,0.7);">{{ $activeServices }} active</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="sa-card sa-stat" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: white;">
            <p class="sa-stat-label" style="color: rgba(255,255,255,0.8);">Categories</p>
            <div class="sa-stat-icon" style="color: rgba(255,255,255,0.5);"><i class="bi bi-tags-fill"></i></div>
            <div class="sa-stat-value" style="color: #fff;">{{ $totalCategories }}</div>
            <small style="color: rgba(255,255,255,0.7);">product categories</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="sa-card sa-stat" style="background: linear-gradient(135deg, #5ee7df 0%, #b490ca 100%); color: white;">
            <p class="sa-stat-label" style="color: rgba(255,255,255,0.8);">Stock Moves</p>
            <div class="sa-stat-icon" style="color: rgba(255,255,255,0.5);"><i class="bi bi-arrow-left-right"></i></div>
            <div class="sa-stat-value" style="color: #fff;">{{ $totalStockMovements }}</div>
            <small style="color: rgba(255,255,255,0.7);">total movements</small>
        </div>
    </div>
</div>

{{-- ============================================================
     STATS ROW 3 — Customers, Requests, Content
     ============================================================ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Customers</p>
            <div class="sa-stat-icon custom-bg-5"><i class="bi bi-person-badge-fill"></i></div>
            <div class="sa-stat-value">{{ $totalCustomers }}</div>
            <small>registered customers</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Service Requests</p>
            <div class="sa-stat-icon custom-bg-6"><i class="bi bi-ticket-perforated-fill"></i></div>
            <div class="sa-stat-value">{{ $totalServiceRequests }}</div>
            <small>{{ $pendingServiceRequests }} pending</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Messages</p>
            <div class="sa-stat-icon custom-bg-7"><i class="bi bi-envelope-fill"></i></div>
            <div class="sa-stat-value">{{ $totalContactMessages }}</div>
            <small>{{ $unreadContactMessages }} unread</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Testimonials</p>
            <div class="sa-stat-icon custom-bg-8"><i class="bi bi-chat-quote-fill"></i></div>
            <div class="sa-stat-value">{{ $totalTestimonials }}</div>
            <small>{{ $totalLinks }} links</small>
        </div>
    </div>
</div>

{{-- ============================================================
     CONTROL CENTER BAR
     ============================================================ --}}
<div class="sa-card mb-4">
    <div class="sa-card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h2 class="h6 fw-bold mb-1">System Control Center</h2>
            <p class="text-muted small mb-0">Manage access, monitor activity, protect data, configure inventory, services, and ERP settings.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('super-admin.users.create') }}" class="btn btn-sa-primary"><i class="bi bi-plus-circle-fill me-1"></i> Create User</a>
            <a href="{{ route('super-admin.inventory.products') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-box-seam-fill me-1"></i> Products</a>
            <a href="{{ route('super-admin.backups.index') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-database-fill me-1"></i> Backup</a>
        </div>
    </div>
</div>

{{-- ============================================================
     CHARTS ROW 1 — Activity Line + Stock Bar + Service Requests Donut
     ============================================================ --}}
<div class="row g-3 mb-4">
    {{-- Activity Trend (Line Chart) --}}
    <div class="col-lg-4">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.9rem;"><i class="bi bi-graph-up me-2 text-primary"></i>Activity Trend (30 days)</h5>
                <div class="chart-container-sm">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- Stock In/Out (Bar Chart) --}}
    <div class="col-lg-5">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.9rem;"><i class="bi bi-boxes me-2 text-success"></i>Stock Movement (30 days)</h5>
                <div class="chart-container">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- Service Requests by Status (Doughnut) --}}
    <div class="col-lg-3">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.9rem;"><i class="bi bi-pie-chart me-2 text-info"></i>Service Requests</h5>
                <div class="chart-container-sm">
                    <canvas id="serviceRequestChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     CHARTS ROW 2 — Products by Category (Horizontal Bar)
     ============================================================ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-12">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.9rem;"><i class="bi bi-bar-chart-fill me-2 text-warning"></i>Products by Category</h5>
                <div class="chart-container" style="height:250px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     RECENT ACTIVITIES + QUICK LINKS
     ============================================================ --}}
<div class="row g-3">
    <div class="col-lg-9">
        <div class="sa-card">
            <div class="sa-card-body p-0">
                <div class="px-3 py-3 border-bottom d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0" style="font-size:0.95rem;"><i class="bi bi-activity me-2 text-primary"></i>Recent Activities</h5>
                    <span class="sa-badge" style="background:#eef2ff;color:#4f46e5;font-size:0.7rem;">{{ $auditLogsCount }} total</span>
                </div>
                @if($recentActivities->count())
                    <div class="table-responsive">
                        <table class="table sa-table">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Description</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $log)
                                <tr>
                                    <td><span class="sa-badge" style="background:#eef2ff;color:#4f46e5;">{{ $log->action }}</span></td>
                                    <td>{{ $log->module ?? '-' }}</td>
                                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->description }}</td>
                                    <td style="font-size:0.8rem;color:#64748b;">{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0 p-3" style="font-size:0.88rem;">No activities recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.95rem;">Quick Links</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-sa-primary"><i class="bi bi-people me-2"></i>Manage Users</a>
                    <a href="{{ route('super-admin.roles.index') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-shield-lock me-2"></i>Roles & Permissions</a>
                    <a href="{{ route('super-admin.inventory.products') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="{{ route('super-admin.inventory.categories') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-tags me-2"></i>Categories</a>
                    <a href="{{ route('super-admin.services.categories') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-gear me-2"></i>Service Categories</a>
                    <a href="{{ route('super-admin.settings.index') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-gear me-2"></i>System Settings</a>
                    <a href="{{ route('super-admin.reports.index') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-bar-chart me-2"></i>View Reports</a>
                    <hr class="my-2">
                    <a href="{{ route('shop.index') }}" target="_blank" class="btn btn-outline-success"><i class="bi bi-cart me-2"></i>View Shop</a>
                </div>
            </div>
        </div>

        @if($outOfStockProducts > 0)
        <div class="sa-card mt-3 border-danger">
            <div class="sa-card-body">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-danger mb-1">Stock Alert</h6>
                        <p class="small text-muted mb-2">{{ $outOfStockProducts }} product(s) are out of stock.</p>
                        <a href="{{ route('super-admin.inventory.products') }}" class="btn btn-sm btn-outline-danger">View Inventory</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($pendingServiceRequests > 0)
        <div class="sa-card mt-3 border-primary">
            <div class="sa-card-body">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-ticket-perforated-fill text-primary fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-primary mb-1">Service Requests</h6>
                        <p class="small text-muted mb-2">{{ $pendingServiceRequests }} pending request(s) need attention.</p>
                        <a href="{{ route('admin.service-requests.index') }}" class="btn btn-sm btn-outline-primary">View Requests</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($unreadContactMessages > 0)
        <div class="sa-card mt-3 border-info">
            <div class="sa-card-body">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-envelope-paper-fill text-info fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-info mb-1">New Messages</h6>
                        <p class="small text-muted mb-2">{{ $unreadContactMessages }} unread contact message(s).</p>
                        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-outline-info">View Messages</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($lowStockProducts > 0)
        <div class="sa-card mt-3 border-warning">
            <div class="sa-card-body">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-warning mb-1">Low Stock Alert</h6>
                        <p class="small text-muted mb-2">{{ $lowStockProducts }} product(s) need restocking.</p>
                        <a href="{{ route('super-admin.inventory.products') }}" class="btn btn-sm btn-outline-warning">Restock Now</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Color palette ──
    const colors = {
        primary: '#4f46e5',
        success: '#16a34a',
        danger: '#ef4444',
        warning: '#f97316',
        info: '#0ea5e9',
        purple: '#8b5cf6',
        pink: '#ec4899',
        teal: '#14b8a6'
    };
    const gradientColors = [
        '#667eea', '#764ba2', '#f093fb', '#f5576c',
        '#fa709a', '#43e97b', '#38f9d7', '#a18cd1',
        '#fbc2eb', '#5ee7df', '#b490ca', '#fee140'
    ];

    // ── Helper: grid color config ──
    const gridConfig = {
        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
        ticks: { font: { size: 9 }, color: '#94a3b8' }
    };

    // ── 1. Activity Line Chart ──
    const actCtx = document.getElementById('activityChart');
    if (actCtx) {
        new Chart(actCtx, {
            type: 'line',
            data: {
                labels: @json($auditChartLabels),
                datasets: [{
                    label: 'Activities',
                    data: @json($auditChartData),
                    borderColor: colors.primary,
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: colors.primary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ...gridConfig, display: false },
                    y: { ...gridConfig, display: false, beginAtZero: true }
                }
            }
        });
    }

    // ── 2. Stock Movement Bar Chart ──
    const stkCtx = document.getElementById('stockChart');
    if (stkCtx) {
        new Chart(stkCtx, {
            type: 'bar',
            data: {
                labels: @json($stockMovementLabels),
                datasets: [
                    {
                        label: 'Stock In',
                        data: @json($stockInData),
                        backgroundColor: 'rgba(22, 163, 74, 0.75)',
                        borderColor: colors.success,
                        borderWidth: 1,
                        borderRadius: 3
                    },
                    {
                        label: 'Stock Out',
                        data: @json($stockOutData),
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                        borderColor: colors.danger,
                        borderWidth: 1,
                        borderRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 10 }, boxWidth: 12, padding: 8 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 7 }, color: '#94a3b8', maxRotation: 45, autoSkip: true, maxTicksLimit: 15 }
                    },
                    y: {
                        ...gridConfig,
                        beginAtZero: true,
                        ticks: { font: { size: 8 }, color: '#94a3b8' }
                    }
                }
            }
        });
    }

    // ── 3. Service Requests Doughnut ──
    const svcCtx = document.getElementById('serviceRequestChart');
    if (svcCtx) {
        const statusLabels = Object.keys(@json($serviceRequestStatuses));
        const statusData = Object.values(@json($serviceRequestStatuses));
        const statusColors = {
            'pending': colors.warning,
            'in_progress': colors.info,
            'completed': colors.success,
            'cancelled': colors.danger,
            'approved': colors.primary,
            'rejected': '#6b7280'
        };
        const bgColors = statusLabels.map(s => statusColors[s] || '#94a3b8');

        new Chart(svcCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels.map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                datasets: [{
                    data: statusData,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 9 }, boxWidth: 10, padding: 6 }
                    }
                }
            }
        });
    }

    // ── 4. Products by Category (Horizontal Bar) ──
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        const catLabels = Object.keys(@json($productsByCategory));
        const catData = Object.values(@json($productsByCategory));
        const catBgColors = catLabels.map((_, i) => gradientColors[i % gradientColors.length]);

        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [{
                    label: 'Products',
                    data: catData,
                    backgroundColor: catBgColors,
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ...gridConfig,
                        beginAtZero: true,
                        ticks: { font: { size: 9 }, color: '#94a3b8', stepSize: 1 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#334155' }
                    }
                }
            }
        });
    }
});
</script>
@endpush