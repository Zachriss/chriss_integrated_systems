@extends('super-admin.layouts.super-admin')
@section('title', 'Profit Reports')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Daily Profit Summary</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Date</th><th>Provider</th><th>Transactions</th><th>Total Fees (TZS)</th><th>Agent Profit (TZS)</th><th>System Profit (TZS)</th></tr></thead>
                    <tbody>
                        @forelse($profitSummaries as $summary)
                        <tr>
                            <td>{{ $summary->report_date->format('d M Y') }}</td>
                            <td>{{ $summary->provider->name }}</td>
                            <td><span class="badge bg-info">{{ $summary->total_transactions }}</span></td>
                            <td>{{ number_format($summary->total_fees) }}</td>
                            <td>{{ number_format($summary->agent_profit) }}</td>
                            <td>{{ number_format($summary->system_profit) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No data yet. Perform transactions first.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $profitSummaries->links() }}
        </div>
    </div>
</div>
@endsection