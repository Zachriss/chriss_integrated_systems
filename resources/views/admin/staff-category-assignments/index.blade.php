@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Staff Category Assignments</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignmentModal">
                    <i class="bi bi-plus-lg"></i> New Assignment
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="assignmentsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Staff Member</th>
                                    <th>Category</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Assigned Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                <tr data-id="{{ $assignment->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $assignment->staff->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assignment->staff->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $assignment->category->name }}</span>
                                    </td>
                                    <td>{{ $assignment->assignedBy->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'active' ? 'success' : 'secondary' }} status-badge">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $assignment->notes ?: '-' }}
                                    </td>
                                    <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary edit-btn"
                                                data-id="{{ $assignment->id }}"
                                                data-user-id="{{ $assignment->user_id }}"
                                                data-category-id="{{ $assignment->category_id }}"
                                                data-status="{{ $assignment->status }}"
                                                data-notes="{{ $assignment->notes }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-{{ $assignment->status === 'active' ? 'warning' : 'success' }} toggle-status-btn"
                                                data-id="{{ $assignment->id }}">
                                                <i class="bi bi-{{ $assignment->status === 'active' ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger delete-btn"
                                                data-id="{{ $assignment->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No assignments yet. Click "New Assignment" to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assignmentForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" id="assignmentId" name="assignment_id" value="">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentModalLabel">New Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Staff Member <span class="text-danger">*</span></label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Select Staff</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="user_id_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Service Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="category_id_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="bi bi-check-lg"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Reset form on modal open
    $('#assignmentModal').on('show.bs.modal', function() {
        if (!$(this).data('editing')) {
            $('#assignmentForm')[0].reset();
            $('#assignmentForm').attr('action', '{{ route("admin.staff-category-assignments.store") }}');
            $('input[name="_method"]').val('POST');
            $('#assignmentId').val('');
            $('#assignmentModalLabel').text('New Assignment');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }
        $(this).data('editing', false);
    });

    // Store assignment
    $('#assignmentForm').on('submit', function(e) {
        e.preventDefault();
        
        let form = $(this);
        let formData = form.serialize();
        let url = form.attr('action');
        let method = $('input[name="_method"]').val();
        
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $('#saveBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        
        $.ajax({
            url: url,
            method: method === 'PUT' ? 'PUT' : 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#assignmentModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                let response = xhr.responseJSON;
                if (response && response.errors) {
                    $.each(response.errors, function(key, val) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').text(val[0]);
                    });
                }
                if (response && response.message) {
                    alert(response.message);
                }
            },
            complete: function() {
                $('#saveBtn').prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save');
            }
        });
    });

    // Edit assignment
    $(document).on('click', '.edit-btn', function() {
        let btn = $(this);
        let modal = $('#assignmentModal');
        
        modal.data('editing', true);
        $('#assignmentId').val(btn.data('id'));
        $('#user_id').val(btn.data('user-id'));
        $('#category_id').val(btn.data('category-id'));
        $('#status').val(btn.data('status'));
        $('#notes').val(btn.data('notes'));
        
        let url = '{{ route("admin.staff-category-assignments.update", ":id") }}'.replace(':id', btn.data('id'));
        $('#assignmentForm').attr('action', url);
        $('input[name="_method"]').val('PUT');
        $('#assignmentModalLabel').text('Edit Assignment');
        
        modal.modal('show');
    });

    // Toggle status
    $(document).on('click', '.toggle-status-btn', function() {
        let btn = $(this);
        let id = btn.data('id');
        
        if (!confirm('Change assignment status?')) return;
        
        $.ajax({
            url: '{{ route("admin.staff-category-assignments.toggle-status", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Failed to toggle status.');
            }
        });
    });

    // Delete assignment
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        
        if (!confirm('Are you sure you want to remove this assignment?')) return;
        
        $.ajax({
            url: '{{ route("admin.staff-category-assignments.destroy", ":id") }}'.replace(':id', id),
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Failed to delete assignment.');
            }
        });
    });
});
</script>
@endpush