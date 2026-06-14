@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <h5 class="mb-3">My Assigned Products</h5>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($products->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-box display-3"></i>
                    <p class="mt-2">No products assigned to you yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Product</th><th>Category</th><th>Qty</th><th>Status</th><th>Date Assigned</th></tr>
                        </thead>
                        <tbody>
                            @foreach($products as $pa)
                            <tr>
                                <td><strong>{{ $pa->product->name ?? 'N/A' }}</strong></td>
                                <td>{{ $pa->product->category->name ?? '—' }}</td>
                                <td>{{ $pa->quantity ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ match($pa->status){
                                        'available'=>'success','sold'=>'secondary','delivered'=>'info',default=>'secondary'
                                    } }}">{{ ucfirst($pa->status) }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($pa->assigned_date)->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection