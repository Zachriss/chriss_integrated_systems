@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Record Opening Balance</h4>
        <a href="{{ route('admin.cash-points.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($existingCashPoint)
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Today's cash point already exists. You are viewing the existing record.
    </div>
    @endif

    <form method="POST" action="{{ route('admin.cash-points.store') }}">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Opening Balances</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">M-Pesa</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="opening_mpesa" value="{{ old('opening_mpesa', $existingCashPoint->opening_mpesa ?? 0) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Airtel</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="opening_airtel" value="{{ old('opening_airtel', $existingCashPoint->opening_airtel ?? 0) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tigo</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="opening_tigo" value="{{ old('opening_tigo', $existingCashPoint->opening_tigo ?? 0) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Halo</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="opening_halo" value="{{ old('opening_halo', $existingCashPoint->opening_halo ?? 0) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cash</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="opening_cash" value="{{ old('opening_cash', $existingCashPoint->opening_cash ?? 0) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $existingCashPoint->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Save Opening Balance
                </button>
            </div>
        </div>
    </form>
</div>
@endsection