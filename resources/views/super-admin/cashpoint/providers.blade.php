@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-gear me-2"></i>Manage Providers</h4>
        <a href="{{ route('super-admin.cashpoint.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list me-2"></i>Providers</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="providersTableBody">
                                @foreach($providers as $p)
                                <tr>
                                    <td><strong>{{ $p->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $p->status === 'active' ? 'success' : 'secondary' }} provider-status-badge" data-id="{{ $p->id }}">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                    <td>{{ $p->creator?->name ?? 'System' }}</td>
                                    <td>{{ $p->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-provider" data-id="{{ $p->id }}" data-name="{{ $p->name }}" data-status="{{ $p->status }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger delete-provider" data-id="{{ $p->id }}" data-name="{{ $p->name }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Provider</h5>
                </div>
                <div class="card-body">
                    <form id="addProviderForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Provider Name</label>
                            <input type="text" class="form-control" id="provider_name" name="name" required placeholder="e.g., Tigo Pesa">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="provider_status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="addProviderBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add Provider
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Provider Modal --}}
<div class="modal fade" id="editProviderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Provider</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProviderForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_provider_id" name="provider_id">
                    <div class="mb-3">
                        <label class="form-label">Provider Name</label>
                        <input type="text" class="form-control" id="edit_provider_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="edit_provider_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateProviderBtn">
                    <i class="bi bi-check-circle me-1"></i> Update Provider
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Add provider
    $('#addProviderForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#addProviderBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Adding...');

        $.ajax({
            url: '{{ route("super-admin.cashpoint.providers.store") }}',
            method: 'POST',
            data: {
                name: $('#provider_name').val(),
                status: $('#provider_status').val(),
                _token: csrfToken
            },
            success: function(res) {
                if (res.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Added', text: res.message, timer: 2000 });
                    }
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || xhr.responseJSON?.errors?.name?.[0] || 'Error adding provider';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else { alert(msg); }
                btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i> Add Provider');
            }
        });
    });

    // Edit provider - show modal
    $('.edit-provider').on('click', function() {
        $('#edit_provider_id').val($(this).data('id'));
        $('#edit_provider_name').val($(this).data('name'));
        $('#edit_provider_status').val($(this).data('status'));
        $('#editProviderModal').modal('show');
    });

    // Update provider
    $('#updateProviderBtn').on('click', function() {
        const id = $('#edit_provider_id').val();
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');

        $.ajax({
            url: '{{ url("/super-admin/cashpoint/providers") }}/' + id,
            method: 'PUT',
            data: {
                name: $('#edit_provider_name').val(),
                status: $('#edit_provider_status').val(),
                _token: csrfToken
            },
            success: function(res) {
                if (res.success) {
                    $('#editProviderModal').modal('hide');
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Updated', text: res.message, timer: 2000 });
                    }
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || xhr.responseJSON?.errors?.name?.[0] || 'Error updating provider';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else { alert(msg); }
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Update Provider');
            }
        });
    });

    // Delete provider
    $('.delete-provider').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        if (!confirm(`Delete provider "${name}"? This action cannot be undone.`)) return;

        $.ajax({
            url: '{{ url("/super-admin/cashpoint/providers") }}/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Deleted', text: res.message, timer: 2000 });
                    }
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error deleting provider';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else { alert(msg); }
            }
        });
    });
});
</script>
@endsection
</write_to_file>