@extends('super-admin.layouts.super-admin')
@section('title', 'Fee Rules')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Provider Fee Rules</h5>
            <div>
                <button class="btn btn-success btn-sm" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-info btn-sm">
                    <i class="bi bi-download"></i> Export CSV
                </a>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createFeeRuleModal">
                    <i class="bi bi-plus-lg"></i> Add Fee Rule
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

            <!-- Filter Form -->
            <form method="GET" action="{{ route('super-admin.cashpoint.fee-rules.index') }}" class="row g-3 mb-4 p-3 bg-light rounded">
                <div class="col-md-4">
                    <label class="form-label">Filter by Provider</label>
                    <select name="provider_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Providers</option>
                        @foreach($providers as $p)
                            <option value="{{ $p->id }}" {{ ($selectedProvider ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Transaction Type</label>
                    <select name="transaction_type" class="form-control" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="deposit" {{ ($selectedType ?? '') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdraw" {{ ($selectedType ?? '') == 'withdraw' ? 'selected' : '' }}>Withdraw</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('super-admin.cashpoint.fee-rules.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-info w-100">
                        <i class="bi bi-download"></i> Export
                    </a>
                </div>
            </form>

            <div class="table-responsive" id="feeRulesTable">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Provider</th>
                            <th>Type</th>
                            <th>Min (TZS)</th>
                            <th>Max (TZS)</th>
                            <th>Fee (TZS)</th>
                            <th>Status</th>
                            <th class="no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeRules as $rule)
                        <tr>
                            <td>{{ $rule->id }}</td>
                            <td>{{ $rule->provider->name }}</td>
                            <td><span class="badge bg-info">{{ $rule->transaction_type }}</span></td>
                            <td>{{ number_format($rule->min_amount) }}</td>
                            <td>{{ $rule->max_amount ? number_format($rule->max_amount) : '∞' }}</td>
                            <td><strong>{{ number_format($rule->fee_amount) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $rule->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $rule->status }}
                                </span>
                            </td>
                            <td class="no-print">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editFeeRuleModal{{ $rule->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('super-admin.cashpoint.fee-rules.destroy', $rule) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Deactivate this fee rule?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                @if(request()->filled('provider_id') || request()->filled('transaction_type'))
                                    No fee rules match your filter criteria.
                                @else
                                    No fee rules defined yet. Click "Add Fee Rule" to create one.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Showing {{ $feeRules->firstItem() ?? 0 }} - {{ $feeRules->lastItem() ?? 0 }} of {{ $feeRules->total() }} rules</small>
                {{ $feeRules->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createFeeRuleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.cashpoint.fee-rules.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">New Fee Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Provider <span class="text-danger">*</span></label>
                    <select name="provider_id" class="form-control" required>
                        <option value="">Select Provider...</option>
                        @foreach($providers as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Transaction Type <span class="text-danger">*</span></label>
                    <select name="transaction_type" class="form-control">
                        <option value="deposit">Deposit</option>
                        <option value="withdraw">Withdraw</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Min Amount (TZS) <span class="text-danger">*</span></label>
                    <input type="number" name="min_amount" class="form-control" value="0" required>
                </div>
                <div class="mb-3">
                    <label>Max Amount (TZS) <small class="text-muted">(leave empty for unlimited)</small></label>
                    <input type="number" name="max_amount" class="form-control" placeholder="e.g. 50000">
                </div>
                <div class="mb-3">
                    <label>Fee Amount (TZS) <span class="text-danger">*</span></label>
                    <input type="number" name="fee_amount" class="form-control" placeholder="e.g. 500" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create Fee Rule</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals (rendered outside table to fix transparency issue) -->
@foreach($feeRules as $rule)
<div class="modal fade" id="editFeeRuleModal{{ $rule->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.cashpoint.fee-rules.update', $rule) }}" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Fee Rule #{{ $rule->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Provider</label>
                    <input type="text" class="form-control" value="{{ $rule->provider->name }}" disabled>
                </div>
                <div class="mb-3">
                    <label>Transaction Type</label>
                    <select name="transaction_type" class="form-control">
                        <option value="deposit" {{ $rule->transaction_type === 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdraw" {{ $rule->transaction_type === 'withdraw' ? 'selected' : '' }}>Withdraw</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Min Amount (TZS)</label>
                    <input type="number" name="min_amount" class="form-control" value="{{ $rule->min_amount }}" required>
                </div>
                <div class="mb-3">
                    <label>Max Amount (TZS) - leave empty for unlimited</label>
                    <input type="number" name="max_amount" class="form-control" value="{{ $rule->max_amount }}">
                </div>
                <div class="mb-3">
                    <label>Fee Amount (TZS)</label>
                    <input type="number" name="fee_amount" class="form-control" value="{{ $rule->fee_amount }}" required>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $rule->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $rule->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Rule</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@push('styles')
<style media="print">
    .no-print { display: none !important; }
    .btn { display: none !important; }
    .card-header div { display: none !important; }
    nav { display: none !important; }
    .sidebar, .header, footer { display: none !important; }
    body { background: white; }
    .card { border: none !important; box-shadow: none !important; }
    .table { font-size: 12px; }
    .table th { background: #333 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>
@endpush
@endsection