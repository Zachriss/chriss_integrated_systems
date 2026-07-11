@extends('super-admin.layouts.super-admin')
@section('title', 'Commission Rules')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Commission Split Rules</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommissionModal"><i class="bi bi-plus-lg"></i> Add Rule</button>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>#</th><th>Provider</th><th>Agent %</th><th>System %</th><th>Total</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($commissionRules as $rule)
                        <tr>
                            <td>{{ $rule->id }}</td>
                            <td>{{ $rule->provider->name }}</td>
                            <td><span class="badge bg-success">{{ $rule->agent_percentage }}%</span></td>
                            <td><span class="badge bg-primary">{{ $rule->system_percentage }}%</span></td>
                            <td>{{ $rule->agent_percentage + $rule->system_percentage }}%</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCommissionModal{{ $rule->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('super-admin.cashpoint.commission-rules.destroy', $rule) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" onclick="return confirm('Deactivate?')"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No commission rules defined.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $commissionRules->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createCommissionModal" tabindex="-1"><div class="modal-dialog"><form action="{{ route('super-admin.cashpoint.commission-rules.store') }}" method="POST" class="modal-content">@csrf
    <div class="modal-header"><h5 class="modal-title">New Commission Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label>Provider</label><select name="provider_id" class="form-control" required>@foreach($providers as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div class="mb-3"><label>Agent Commission %</label><input type="number" name="agent_percentage" class="form-control" value="50" min="0" max="100" required></div>
        <div class="mb-3"><label>System Commission %</label><input type="number" name="system_percentage" class="form-control" value="50" min="0" max="100" required></div>
        <small class="text-muted">Total of agent + system should equal 100%</small>
    </div>
    <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
</form></div></div>

<!-- Edit Modals (outside table to fix transparency) -->
@foreach($commissionRules as $rule)
<div class="modal fade" id="editCommissionModal{{ $rule->id }}" tabindex="-1"><div class="modal-dialog"><form action="{{ route('super-admin.cashpoint.commission-rules.update', $rule) }}" method="POST" class="modal-content">@csrf @method('PUT')<div class="modal-header"><h5 class="modal-title">Edit Commission Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
    <div class="mb-3"><label>Agent Percentage</label><input type="number" name="agent_percentage" class="form-control" value="{{ $rule->agent_percentage }}" min="0" max="100" required></div>
    <div class="mb-3"><label>System Percentage</label><input type="number" name="system_percentage" class="form-control" value="{{ $rule->system_percentage }}" min="0" max="100" required></div>
    <div class="mb-3"><label>Status</label><select name="status" class="form-control"><option value="active" {{ $rule->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $rule->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div></form></div></div>
@endforeach
@endsection