@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Cash Point Management</h4>
        <div>
            <a href="{{ route('super-admin.cashpoint.sessions') }}" class="btn btn-primary">
                <i class="bi bi-table me-1"></i> All Sessions
            </a>
            <a href="{{ route('super-admin.cashpoint.providers') }}" class="btn btn-outline-primary ms-2">
                <i class="bi bi-gear me-1"></i> Providers
            </a>
            <a href="{{ route('super-admin.cashpoint.audit-logs') }}" class="btn btn-outline-secondary ms-2">
                <i class="bi bi-activity me-1"></i> Audit Logs
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <div class="small opacity-75">Total Staff</div>
                    <strong class="fs-3">{{ $totalStaff }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body text-center">
                    <div class="small opacity-75">Sessions Today</div>
                    <strong class="fs-3">{{ $todaySessions }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-warning text-dark">
                <div class="card-body text-center">
                    <div class="small opacity-75">Open</div>
                    <strong class="fs-3">{{ $openSessions }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-secondary text-white">
                <div class="card-body text-center">
                    <div class="small opacity-75">Closed Today</div>
                    <strong class="fs-3">{{ $closedSessions }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-phone me-2"></i>Mobile Money Providers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Provider</th><th>Status</th><th>Created</th></tr>
                            </thead>
                            <tbody>
                                @foreach($providers as $p)
                                <tr>
                                    <td><strong>{{ $p->name }}</strong></td>
                                    <td><span class="badge bg-{{ $p->status === 'active' ? 'success' : 'secondary' }}">{{ $p->status }}</span></td>
                                    <td>{{ $p->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>System Overview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Sessions All Time</span>
                            <strong>{{ $totalSessions }}</strong>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active Providers</span>
                            <strong>{{ $providers->where('status', 'active')->count() }}</strong>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Staff open sessions each day with opening balances. The system carries forward closing balances to the next day automatically.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</write_to_file>