@extends('super-admin.layouts.super-admin')

@section('title', 'Stock In')

@section('content')
<div class="sa-page-header">
    <h1>Stock In</h1>
    <p>Add stock to inventory products.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Product</label>
                    <select class="form-select">
                        <option value="">Select product...</option>
                        @foreach(App\Models\Product::all() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock ?? 0 }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" placeholder="0" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit Cost (TSh)</label>
                    <input type="number" class="form-control" placeholder="0.00">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes / Reference</label>
                    <input type="text" class="form-control" placeholder="e.g. Supplier invoice #123">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-plus-circle me-1"></i> Add Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection