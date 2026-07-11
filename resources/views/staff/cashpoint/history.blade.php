@extends('layouts.chrissDashboardLayout')
@section('title', 'Transaction History')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">My Transaction History</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Cash Point</th>
                            <th>Provider</th>
                            <th>Type</th>
                            <th>Amount (TZS)</th>
                            <th>Fee (TZS)</th>
                            <th>My Commission</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td>{{ $txn->id }}</td>
                            <td>{{ $txn->transaction_date->format('d M Y') }}</td>
                            <td>{{ $txn->cashPoint?->name ?? 'N/A' }}</td>
                            <td>{{ $txn->provider?->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $txn->transaction_type === 'deposit' ? 'success' : 'danger' }}">
                                    {{ ucfirst($txn->transaction_type) }}
                                </span>
                            </td>
                            <td>{{ number_format($txn->amount) }}</td>
                            <td>{{ number_format($txn->fee) }}</td>
                            <td>{{ number_format($txn->agent_commission) }}</td>
                            <td><code>{{ $txn->reference_number ?? 'N/A' }}</code></td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center">No transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection