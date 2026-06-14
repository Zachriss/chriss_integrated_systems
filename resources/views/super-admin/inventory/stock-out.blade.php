@extends('super-admin.layouts.super-admin')

@section('title', 'Stock Out')

@section('content')
<div class="sa-page-header">
    <h1>Stock Out</h1>
    <p>Record stock out / product usage.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Product</label>
                    <select class="form-select">
                        <option value="">Select product...</option>
                        @foreach(App\Models\Product::where('stock', '>', 0)->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Available: {{ $p->stock ?? 0 }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" placeholder="0" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Reason</label>
                    <select class="form-select">
                        <option>Sale</option>
                        <option>Internal Use</option>
                        <option>Damaged</option>
                        <option>Expired</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" rows="2" placeholder="Reason for stock out..."></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-dash-circle me-1"></i> Record Stock Out</button>
            </div>
        </form>
    </div>
</div>
@endsection