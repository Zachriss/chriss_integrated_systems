@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Cash Points</h4>
        @if(!$existingCashPoint)
        <a href="{{ route('admin.cash-points.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Cash Point
        </a>
        @endif
    </div>

    @if($existingCashPoint)
    <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <div>
            Today's cash point has already been recorded. You can view and manage it below.
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Cash Point History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Opening Total</th>
                            <th>Closing Total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashPoints as $cashPoint)
                        <tr>
                            <td>{{ $cashPoint->date->format('M d, Y') }}</td>
                            <td>KES {{ number_format($cashPoint->total_opening, 2) }}</td>
                            <td>KES {{ number_format($cashPoint->total_closing, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.cash-points.show', $cashPoint->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No cash points recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cashPoints->hasPages())
        <div class="card-footer bg-white">
            {{ $cashPoints->links() }}
        </div>
        @endif
    </div>
</div>
@endsection