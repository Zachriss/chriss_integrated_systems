@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Cash Point Overview</h4>
        @if(!$cashPoint)
        <a href="{{ route('admin.cash-points.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> New Cash Point</a>
        @endif
    </div>

    @if($cashPoint)
    {{-- Per-channel Cards from Staff Data --}}
    <h5 class="mb-3">Daily Channel Balances</h5>
    <div class="row g-3 mb-4">
        @foreach($channels as $ch)
        @php 
            $opening = $openings->firstWhere('payment_channel_id', $ch->id);
            $closing = $closings->firstWhere('payment_channel_id', $ch->id);
            $opBal = $opening?->opening_balance ?? 0;
            $clBal = $closing?->closing_balance ?? 0;
            $diff = $closing ? ($clBal - $opBal) : 0;
        @endphp
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted small">{{ $ch->name }}</h6>
                    <div class="row g-1 small">
                        <div class="col-6 bg-light rounded p-1">
                            <div class="text-muted" style="font-size:.65rem">OPENING</div>
                            <strong>{{ number_format($opBal, 0) }}</strong>
                            @if($opening)<i class="bi bi-lock-fill text-muted ms-1" style="font-size:.6rem"></i>@endif
                        </div>
                        <div class="col-6 bg-light rounded p-1">
                            <div class="text-muted" style="font-size:.65rem">CLOSING</div>
                            <strong>{{ $closing ? number_format($clBal, 0) : '—' }}</strong>
                            @if($closing)<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.6rem"></i>@endif
                        </div>
                    </div>
                    @if($closing)
                    <div class="mt-2">
                        <small class="text-muted">Diff:</small>
                        <strong class="text-{{ $diff >= 0 ? 'success' : 'danger' }}">{{ number_format($diff, 0) }}</strong>
                    </div>
                    @elseif($opening)
                    <div class="mt-2"><span class="badge bg-warning text-dark">Awaiting closing</span></div>
                    @else
                    <div class="mt-2"><span class="badge bg-secondary">Not recorded</span></div>
                    @endif
                    <small class="text-muted d-block mt-1">Staff: {{ $opening?->createdBy?->name ?? $closing?->recordedBy?->name ?? '—' }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Summary Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Balance Summary</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Channel</th><th>Opening</th><th>Closing</th><th>Difference</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($channels as $ch)
                        @php 
                            $o = $openings->firstWhere('payment_channel_id', $ch->id);
                            $c = $closings->firstWhere('payment_channel_id', $ch->id);
                            $ob = $o?->opening_balance ?? 0;
                            $cb = $c?->closing_balance ?? 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $ch->name }}</strong></td>
                            <td>TZS {{ number_format($ob, 0) }}</td>
                            <td>TZS {{ $c ? number_format($cb, 0) : '—' }}</td>
                            <td class="text-{{ $c && ($cb-$ob)>=0 ? 'success' : ($c ? 'danger' : '') }}">{{ $c ? number_format($cb-$ob, 0) : '—' }}</td>
                            <td>{!! $c ? '<span class="badge bg-success">Closed</span>' : ($o ? '<span class="badge bg-warning text-dark">Open</span>' : '<span class="badge bg-secondary">Pending</span>') !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Opening Details --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Opening Balances (Staff)</h6></div>
                <div class="card-body p-0">
                    @if($openings->isEmpty())
                        <div class="text-muted text-center py-3">Not recorded yet</div>
                    @else
                    <table class="table table-sm mb-0">
                        <tbody>@foreach($openings as $o)
                            <tr><td>{{ $o->paymentChannel->name }}</td><td class="fw-bold">TZS {{ number_format($o->opening_balance,0) }}</td><td><small class="text-muted">{{ $o->createdBy->name ?? '' }}</small></td><td><i class="bi bi-lock-fill text-muted"></i></td></tr>
                        @endforeach</tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Closing Balances (Staff)</h6></div>
                <div class="card-body p-0">
                    @if($closings->isEmpty())
                        <div class="text-muted text-center py-3">Not recorded yet</div>
                    @else
                    <table class="table table-sm mb-0">
                        <tbody>@foreach($closings as $c)
                            <tr><td>{{ $c->paymentChannel->name }}</td><td class="fw-bold">TZS {{ number_format($c->closing_balance,0) }}</td><td><small class="text-muted">{{ $c->recordedBy->name ?? '' }}</small></td><td><strong class="text-{{ $c->difference>=0?'success':'danger' }}">{{ number_format($c->difference,0) }}</strong></td></tr>
                        @endforeach</tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="text-center py-5">
        <i class="bi bi-cash-stack display-4 text-muted"></i>
        <h5 class="mt-3">No Cash Point Today</h5>
        <p class="text-muted">Create a cash point or wait for staff to record opening balances.</p>
        <a href="{{ route('admin.cash-points.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Create Cash Point</a>
    </div>
    @endif

    {{-- History --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex justify-content-between">
            <h5 class="mb-0">Cash Point History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>@forelse($cashPoints as $cp)
                        <tr><td>{{ $cp->date->format('M d, Y') }}</td>
                            <td>@if($cp->closings()->count() > 0)<span class="badge bg-success">Complete</span>@elseif($cp->openings()->count() > 0)<span class="badge bg-warning text-dark">Partial</span>@else<span class="badge bg-secondary">Empty</span>@endif</td>
                            <td><a href="{{ route('admin.cash-points.show', $cp->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No cash points yet.</td></tr>
                    @endforelse</tbody>
                </table>
            </div>
            @if($cashPoints->hasPages())
            <div class="p-3">{{ $cashPoints->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection