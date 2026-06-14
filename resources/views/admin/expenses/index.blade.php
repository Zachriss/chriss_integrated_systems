@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Expenses Management</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal" onclick="resetForm()">
            <i class="bi bi-plus-circle me-1"></i> Add Expense
        </button>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-2"><label class="form-label small">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
                <div class="col-md-2"><label class="form-label small">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
                <div class="col-md-2"><label class="form-label small">Category</label>
                    <select name="category" class="form-select form-select-sm"><option value="">All</option>
                        @foreach($categories as $key => $label)<option value="{{ $key }}" {{ request('category')===$key ? 'selected' : '' }}>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Filter</button><a href="{{ route('admin.expenses.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
            </form>
            @if($expenses->isEmpty())<div class="text-center py-4 text-muted">No expenses found.</div>@else
            <div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Recorded By</th><th></th></tr></thead>
            <tbody>@foreach($expenses as $e)<tr>
                <td>{{ $e->title }}</td><td><span class="badge bg-secondary">{{ ucfirst($e->category) }}</span></td>
                <td class="text-danger fw-semibold">TZS {{ number_format($e->amount, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') }}</td>
                <td>{{ $e->creator->name ?? 'N/A' }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary edit-btn" data-expense="{{ json_encode($e) }}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $e->id }}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>@endforeach</tbody></table></div>
            <div class="mt-3">{{ $expenses->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Expense Modal --}}
<div class="modal fade" id="expenseModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="modalTitle">Add Expense</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="expenseForm"><input type="hidden" id="expenseId" name="id">
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Title *</label><input type="text" name="title" id="expTitle" class="form-control" required></div>
            <div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label">Amount (TZS) *</label><input type="number" name="amount" id="expAmount" class="form-control" step="0.01" min="0" required></div>
            <div class="col-md-6"><label class="form-label">Category *</label><select name="category" id="expCategory" class="form-select" required><option value="">Select</option>
                @foreach($categories as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div></div>
            <div class="mb-3"><label class="form-label">Date *</label><input type="date" name="expense_date" id="expDate" class="form-control" value="{{ today()->toDateString() }}" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="expDesc" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="submitBtn">Save</button></div>
    </form>
</div></div></div>
@endsection

@section('scripts')
<script>
const modal = new bootstrap.Modal('#expenseModal');
const form = document.getElementById('expenseForm');
let editId = null;

function resetForm(){form.reset();editId=null;document.getElementById('expenseId').value='';document.getElementById('modalTitle').textContent='Add Expense';document.getElementById('expDate').value='{{ today()->toDateString() }}';}
document.querySelectorAll('.edit-btn').forEach(b=>b.addEventListener('click',function(){
    const d=JSON.parse(this.dataset.expense);
    editId=d.id;document.getElementById('expenseId').value=d.id;document.getElementById('expTitle').value=d.title;
    document.getElementById('expAmount').value=d.amount;document.getElementById('expCategory').value=d.category;
    document.getElementById('expDate').value=d.expense_date;
    document.getElementById('expDesc').value=d.description||'';
    document.getElementById('modalTitle').textContent='Edit Expense';modal.show();
}));

document.querySelectorAll('.delete-btn').forEach(b=>b.addEventListener('click',function(){
    if(!confirm('Delete this expense?'))return;
    const id=this.dataset.id;
    fetch('{{ route("admin.expenses.destroy", "_ID_") }}'.replace('_ID_',id),{
        method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}
    }).then(r=>r.json()).then(data=>{if(data.success)location.reload();else alert(data.message);});
}));

form.addEventListener('submit',function(e){
    e.preventDefault();
    const btn=document.getElementById('submitBtn');btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
    const formData=new FormData(form);const data=Object.fromEntries(formData.entries());
    const url=editId?'{{ route("admin.expenses.update", "_ID_") }}'.replace('_ID_',editId):'{{ route("admin.expenses.store") }}';
    const method=editId?'PUT':'POST';
    fetch(url,{method,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify(data)})
    .then(r=>r.json()).then(data=>{
        btn.disabled=false;btn.innerHTML='Save';
        if(data.success){modal.hide();location.reload();}else{alert(data.message||'Error');}
    }).catch(()=>{btn.disabled=false;btn.innerHTML='Save';alert('Error');});
});
</script>
@endsection