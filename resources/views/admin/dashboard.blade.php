@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    {{-- TODAY SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3"><div class="d-flex align-items-center gap-2">
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-3"><i class="bi bi-cash-stack fs-5"></i></div>
                    <div class="min-w-0"><div class="text-muted small">Today's Income</div><h5 class="mb-0">TZS {{ number_format($todayStaffIncome, 2) }}</h5></div>
                </div></div>
            </div>
        </div>
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3"><div class="d-flex align-items-center gap-2">
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3"><i class="bi bi-cart-dash fs-5"></i></div>
                    <div class="min-w-0"><div class="text-muted small">Today's Expenses</div><h5 class="mb-0">TZS {{ number_format($todayExpenses, 2) }}</h5></div>
                </div></div>
            </div>
        </div>
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3"><div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3"><i class="bi bi-graph-up fs-5"></i></div>
                    <div class="min-w-0"><div class="text-muted small">Today's Profit</div><h5 class="mb-0 {{ $todayProfit >= 0 ? 'text-success' : 'text-danger' }}">TZS {{ number_format($todayProfit, 2) }}</h5></div>
                </div></div>
            </div>
        </div>
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3"><div class="d-flex align-items-center gap-2">
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3"><i class="bi bi-list-check fs-5"></i></div>
                    <div class="min-w-0"><div class="text-muted small">Pending Tasks</div><h5 class="mb-0">{{ $pendingTasks }}</h5><small class="text-muted">{{ $completedTasks }} completed today</small></div>
                </div></div>
            </div>
        </div>
    </div>

    {{-- MONTHLY SUMMARY --}}
    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body text-center">
                <h6 class="text-muted">Monthly Income</h6><h3 class="text-success">TZS {{ number_format($monthlyStaffIncome, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body text-center">
                <h6 class="text-muted">Monthly Expenses</h6><h3 class="text-danger">TZS {{ number_format($monthlyExpenses, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body text-center">
                <h6 class="text-muted">Monthly Profit</h6><h3 class="{{ $monthlyProfit >= 0 ? 'text-success' : 'text-danger' }}">TZS {{ number_format($monthlyProfit, 2) }}</h3>
            </div></div>
        </div>
    </div>

    {{-- CUSTOMER ORDERS / SERVICE REQUESTS --}}
    <div class="row g-3 mb-4">
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100 text-bg-danger">
                <div class="card-body text-center py-2">
                    <h4 class="mb-0">{{ $pendingServiceRequests }}</h4>
                    <small>Pending</small>
                    @if($pendingServiceRequests > 0)
                        <div><a href="{{ route('admin.service-requests.index', ['status' => 'pending']) }}" class="text-white small">View &raquo;</a></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100 text-bg-info">
                <div class="card-body text-center py-2">
                    <h4 class="mb-0">{{ $inProgressServiceRequests }}</h4>
                    <small>In Progress</small>
                    <div><a href="{{ route('admin.service-requests.index', ['status' => 'in_progress']) }}" class="text-dark small">View &raquo;</a></div>
                </div>
            </div>
        </div>
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100 text-bg-success">
                <div class="card-body text-center py-2">
                    <h4 class="mb-0">{{ $completedServiceRequests }}</h4>
                    <small>Completed</small>
                    <div><a href="{{ route('admin.service-requests.index', ['status' => 'completed']) }}" class="text-white small">View &raquo;</a></div>
                </div>
            </div>
        </div>
        <div class="col-3 col-xl-3">
            <div class="card border-0 shadow-sm h-100 text-bg-primary">
                <div class="card-body text-center py-2">
                    <h4 class="mb-0">{{ $totalServiceRequests }}</h4>
                    <small>Total</small>
                    <div><a href="{{ route('admin.service-requests.index') }}" class="text-white small">View all &raquo;</a></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Pending Orders --}}
    @if($recentPendingRequests->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Recent Pending Orders ({{ $pendingServiceRequests }} total)</h6>
            <a href="{{ route('admin.service-requests.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-inbox me-1"></i> View All Pending
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Notes</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPendingRequests as $req)
                        <tr>
                            <td>#{{ $req->id }}</td>
                            <td>
                                <strong>{{ $req->customer->full_name ?? $req->customer->name ?? 'N/A' }}</strong>
                                @if($req->customer && $req->customer->phone)
                                    <br><small class="text-muted">{{ $req->customer->phone }}</small>
                                @endif
                            </td>
                            <td>{{ $req->service->name ?? 'N/A' }}</td>
                            <td><small class="text-muted">{{ Str::limit($req->notes, 30) }}</small></td>
                            <td><small>{{ $req->created_at->format('M d, Y g:i A') }}</small></td>
                            <td>
                                <a href="{{ route('admin.service-requests.show', $req) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Process
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TOP PERFORMERS + ALERTS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent"><h6 class="mb-0"><i class="bi bi-person-badge me-1"></i> Top Staff (This Month)</h6></div>
                <div class="card-body p-0">
                    @forelse($topStaff as $s)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ $s->staff->name ?? 'N/A' }}</span><strong class="text-success">TZS {{ number_format($s->total, 2) }}</strong></div>
                    @empty<div class="text-muted text-center py-3">No data</div>@endforelse
                </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent"><h6 class="mb-0"><i class="bi bi-gear me-1"></i> Top Services (This Month)</h6></div>
                <div class="card-body p-0">
                    @forelse($topServices as $sv)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ $sv->service->name ?? 'N/A' }}</span><strong class="text-success">TZS {{ number_format($sv->total, 2) }}</strong></div>
                    @empty<div class="text-muted text-center py-3">No data</div>@endforelse
                </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent"><h6 class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Low Stock Alerts</h6></div>
                <div class="card-body p-0">
                    @forelse($lowStockProducts as $p)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ $p->name }}</span><span class="badge bg-danger">{{ $p->quantity }} left</span></div>
                    @empty<div class="text-muted text-center py-3">All stock OK</div>@endforelse
                </div></div>
        </div>
    </div>

    {{-- CASH POINT + RECENT EXPENSES --}}
    <div class="row g-4">
        <div class="col-md-6">
            @if($todayCashPoint)
            <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Today's Cash Point Summary</h5></div>
            <div class="card-body"><div class="row g-4">
                <div class="col-md-6"><h6 class="text-muted mb-3">Opening</h6>
                    <div class="row g-2"><div class="col-6"><span>M-Pesa:</span> <strong>TZS {{ number_format($todayCashPoint->opening_mpesa, 2) }}</strong></div>
                    <div class="col-6"><span>Airtel:</span> <strong>TZS {{ number_format($todayCashPoint->opening_airtel, 2) }}</strong></div>
                    <div class="col-12"><span>Cash:</span> <strong>TZS {{ number_format($todayCashPoint->opening_cash, 2) }}</strong></div></div>
                </div>
                <div class="col-md-6"><h6 class="text-muted mb-3">Closing</h6>
                    <div class="row g-2"><div class="col-6"><span>M-Pesa:</span> <strong>TZS {{ number_format($todayCashPoint->closing_mpesa, 2) }}</strong></div>
                    <div class="col-6"><span>Airtel:</span> <strong>TZS {{ number_format($todayCashPoint->closing_airtel, 2) }}</strong></div>
                    <div class="col-12"><span>Cash:</span> <strong>TZS {{ number_format($todayCashPoint->closing_cash, 2) }}</strong></div></div>
                </div>
            </div><a href="{{ route('admin.cash-points.show', $todayCashPoint->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-cash-coin me-1"></i> Manage</a></div></div>
            @else
            <div class="card border-0 shadow-sm"><div class="card-body text-center py-4"><i class="bi bi-cash-stack text-muted" style="font-size:3rem;"></i><h5>No Cash Point Today</h5><a href="{{ route('admin.cash-points.create') }}" class="btn btn-primary btn-sm">Record Opening</a></div></div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent d-flex justify-content-between"><h6 class="mb-0"><i class="bi bi-cart-dash me-1"></i> Recent Expenses</h6><a href="{{ route('admin.expenses.index') }}" class="btn btn-sm btn-outline-secondary">View All</a></div>
            <div class="card-body p-0">
                @forelse($recentExpenses as $exp)
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><div><strong>{{ $exp->title }}</strong><br><small class="text-muted">{{ ucfirst($exp->category) }} · {{ \Carbon\Carbon::parse($exp->expense_date)->format('M d') }}</small></div><span class="text-danger fw-semibold">TZS {{ number_format($exp->amount, 2) }}</span></div>
                @empty<div class="text-muted text-center py-3">No expenses recorded</div>@endforelse
            </div></div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="card border-0 shadow-sm mt-4"><div class="card-header bg-white py-3"><h5 class="mb-0">Quick Actions</h5></div>
    <div class="card-body"><div class="row g-3">
        <div class="col-md-2"><a href="{{ route('admin.cash-points.create') }}" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-cash-stack d-block fs-4 mb-1"></i>Cash Point</a></div>
        <div class="col-md-2"><a href="{{ route('admin.staff-tasks.create') }}" class="btn btn-outline-success w-100 py-3"><i class="bi bi-person-plus d-block fs-4 mb-1"></i>Assign Task</a></div>
        <div class="col-md-2"><a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-danger w-100 py-3"><i class="bi bi-cart-dash d-block fs-4 mb-1"></i>Expenses</a></div>
        <div class="col-md-2"><a href="{{ route('admin.services.index') }}" class="btn btn-outline-info w-100 py-3"><i class="bi bi-gear d-block fs-4 mb-1"></i>Services</a></div>
        <div class="col-md-2"><a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-warning w-100 py-3"><i class="bi bi-box-seam d-block fs-4 mb-1"></i>Inventory</a></div>
        <div class="col-md-2"><a href="{{ route('admin.finance.profit-loss') }}" class="btn btn-outline-dark w-100 py-3"><i class="bi bi-graph-up d-block fs-4 mb-1"></i>Profit/Loss</a></div>
    </div></div></div>
</div>
@endsection