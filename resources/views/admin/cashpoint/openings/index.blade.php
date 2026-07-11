@extends('layouts.chrissDashboardLayout')
@section('title', 'Opening Balances')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Set Opening Balances</h5>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

            <form action="{{ route('admin.cashpoint.openings.store') }}" method="POST" class="row g-3 mb-4">
                @csrf
                <div class="col-md-3">
                    <label>Cash Point</label>
                    <select name="cash_point_id" class="form-control" required>
                        @foreach($cashPoints as $cp)
                            <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Provider</label>
                    <select name="provider_id" class="form-control" required>
                        @foreach($providers as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Opening Balance (TZS)</label>
                    <input type="number" name="opening_balance" class="form-control" value="0" min="0" required>
                </div>
                <div class="col-md-2">
                    <label>Date</label>
                    <input type="date" name="opening_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Set Opening</button>
                </div>
            </form>

            <hr>
            <h6>Today's Opening Balances</h6>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Cash Point</th><th>Provider</th><th>Balance (TZS)</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($openings as $opening)
                        <tr>
                            <td>{{ $opening->cashPoint?->name ?? 'N/A' }}</td>
                            <td>{{ $opening->provider?->name ?? 'N/A' }}</td>
                            <td>{{ number_format($opening->opening_balance) }}</td>
                            <td>{{ $opening->opening_date->format('d M Y') }}</td>
                            <td>
                                @if($opening->is_locked)
                                    <span class="badge bg-secondary">Locked</span>
                                @else
                                    <span class="badge bg-success">Open</span>
                                @endif
                            </td>
                            <td>
                                @if(!$opening->is_locked)
                                <form action="{{ route('admin.cashpoint.openings.lock', $opening) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-warning" onclick="return confirm('Lock this opening balance?')"><i class="bi bi-lock"></i> Lock</button>
                                </form>
                                @else
                                <form action="{{ route('admin.cashpoint.openings.unlock', $opening) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Unlock? Only Super Admin can do this.')"><i class="bi bi-unlock"></i> Unlock</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No opening balances set for today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection