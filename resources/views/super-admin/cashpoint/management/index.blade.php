@extends('super-admin.layouts.super-admin')
@section('title', 'Cash Point Management')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cash Points</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCashPointModal"><i class="bi bi-plus-lg"></i> New Cash Point</button>
                </div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>#</th><th>Name</th><th>Status</th><th>Staff Assigned</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse($cashPoints as $cp)
                                <tr>
                                    <td>{{ $cp->id }}</td>
                                    <td>{{ $cp->name }}</td>
                                    <td><span class="badge bg-{{ $cp->status === 'active' ? 'success' : 'danger' }}">{{ $cp->status }}</span></td>
                                    <td>{{ $cp->staff_assignments_count }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCashPointModal{{ $cp->id }}"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#assignStaffModal{{ $cp->id }}"><i class="bi bi-person-plus"></i></button>
                                        <form action="{{ route('super-admin.cashpoint.management.cash-points.destroy', $cp) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Deactivate this cash point?')"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">No cash points created yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $cashPoints->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createCashPointModal" tabindex="-1"><div class="modal-dialog"><form action="{{ route('super-admin.cashpoint.management.cash-points.store') }}" method="POST" class="modal-content">@csrf
    <div class="modal-header"><h5 class="modal-title">New Cash Point</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" placeholder="e.g. Main Branch" required></div></div>
    <div class="modal-footer"><button type="submit" class="btn btn-primary">Create</button></div>
</form></div></div>

<!-- Edit & Assign Modals (outside table to fix transparency) -->
@foreach($cashPoints as $cp)
<div class="modal fade" id="editCashPointModal{{ $cp->id }}" tabindex="-1"><div class="modal-dialog"><form action="{{ route('super-admin.cashpoint.management.cash-points.update', $cp) }}" method="POST" class="modal-content">@csrf @method('PUT')<div class="modal-header"><h5 class="modal-title">Edit Cash Point</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $cp->name }}" required></div>
    <div class="mb-3"><label>Status</label><select name="status" class="form-control"><option value="active" {{ $cp->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $cp->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div></form></div></div>

<div class="modal fade" id="assignStaffModal{{ $cp->id }}" tabindex="-1"><div class="modal-dialog"><form action="{{ route('super-admin.cashpoint.management.assign-staff') }}" method="POST" class="modal-content">@csrf<input type="hidden" name="cash_point_id" value="{{ $cp->id }}"><div class="modal-header"><h5 class="modal-title">Assign Staff to {{ $cp->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
    <div class="mb-3"><label>Staff Member</label><select name="staff_id" class="form-control" required><option value="">Select Staff...</option>@foreach(\App\Models\User::where('role', 'staff')->where('status', 'active')->get() as $staff)<option value="{{ $staff->id }}">{{ $staff->name }}</option>@endforeach</select></div>
    <div class="mb-3"><label>Start Date</label><input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary">Assign</button></div></form></div></div>
@endforeach
@endsection