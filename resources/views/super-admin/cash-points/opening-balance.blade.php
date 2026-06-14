@extends('super-admin.layouts.super-admin')

@section('title', 'Opening Balance Setup')

@section('content')
<div class="sa-page-header">
    <h1>Opening Balance Setup</h1>
    <p>Configure opening balances for cash point accounts.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Account</label>
                    <select class="form-select">
                        <option>Main Operating Account</option>
                        <option>Petty Cash</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Opening Balance (TSh)</label>
                    <input type="number" class="form-control" placeholder="0.00">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Notes</label>
                    <input type="text" class="form-control" placeholder="Optional notes">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-check2 me-1"></i> Save Balance</button>
            </div>
        </form>
    </div>
</div>
@endsection