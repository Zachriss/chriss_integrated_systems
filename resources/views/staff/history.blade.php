@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('staff.income.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Record New Income
        </a>
    </div>

    {{-- Category Summary Cards --}}
    @if($categorySummaries->isNotEmpty())
    <div class="row g-3 mb-4">
        @foreach($categorySummaries as $summary)
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-folder2-open fs-3 text-primary me-2"></i>
                        <h6 class="mb-0 fw-semibold">{{ $summary->category->name ?? 'Uncategorized' }}</h6>
                    </div>
                    <h4 class="text-success fw-bold mb-1">TZS {{ number_format($summary->total_amount, 2) }}</h4>
                    <small class="text-muted">{{ $summary->total_records }} record{{ $summary->total_records !== 1 ? 's' : '' }}</small>
                </div>
            </div>
        </div>
        @endforeach
        {{-- Grand Total Card --}}
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-gradient text-white">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-calculator fs-3 me-2"></i>
                        <h6 class="mb-0 fw-semibold text-white">Grand Total</h6>
                    </div>
                    <h4 class="fw-bold mb-1">TZS {{ number_format($grandTotal, 2) }}</h4>
                    <small class="opacity-75">All categories combined</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Form --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.income.history') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('staff.income.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($records->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-currency-exchange" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3 mb-0">No income records found.</p>
                    <a href="{{ route('staff.income.create') }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Record your first income
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Service</th>
                                <th>Task</th>
                                <th class="text-end">Amount (TZS)</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                <td>{{ $record->category->name ?? 'N/A' }}</td>
                                <td>{{ $record->service->name ?? 'N/A' }}</td>
                                <td>
                                    @if($record->task)
                                        <a href="{{ route('staff.tasks.show', $record->task) }}" class="text-decoration-none">
                                            {{ \Illuminate\Support\Str::limit($record->task->title, 20) }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($record->amount, 2) }}</td>
                                <td>{{ $record->quantity }}</td>
                                <td class="text-end fw-semibold text-success">{{ number_format($record->amount * $record->quantity, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staff.income.edit', $record) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection