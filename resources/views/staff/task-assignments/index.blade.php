@extends('layouts.app')

@section('title', 'My Assigned Categories')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-0">
                <i class="bi bi-list-check"></i> My Assigned Categories
            </h4>
            <p class="text-muted">Service categories assigned to you</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($assignments->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="mt-2 text-muted">No categories assigned to you yet.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Assigned Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $assignment->category->name }}</strong></td>
                                    <td>{{ $assignment->assignedBy->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $assignment->notes ?: '-' }}</td>
                                    <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($assignment->status === 'active')
                                        <a href="{{ route('staff.task-assignments.show-category', $assignment->category_id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-cash-coin"></i> Record Income
                                        </a>
                                        @else
                                        <span class="text-muted small">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection