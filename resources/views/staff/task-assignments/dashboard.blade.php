@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-0">
                <i class="bi bi-list-check"></i> My Tasks
            </h4>
            <p class="text-muted">Service categories assigned to you</p>
        </div>
    </div>

    @if($assignments->isEmpty())
    <div class="row">
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="mt-3 text-muted">No tasks assigned yet.</p>
        </div>
    </div>
    @else
    <div class="row">
        @foreach($stats as $stat)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $stat['assignment']->category->name }}</h5>
                            <small class="text-muted">
                                Assigned {{ $stat['assignment']->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $stat['assignment']->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($stat['assignment']->status) }}
                        </span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <div class="h5 mb-0 text-primary">{{ $stat['total_services'] }}</div>
                                <small class="text-muted">Total Services</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <div class="h5 mb-0 text-success">{{ $stat['active_services'] }}</div>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                    </div>

                    @if($stat['assignment']->notes)
                    <p class="small text-muted mb-3">
                        <i class="bi bi-chat-dots"></i> {{ $stat['assignment']->notes }}
                    </p>
                    @endif

                    <a href="{{ route('staff.task-assignments.show-category', $stat['assignment']->category_id) }}" 
                       class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-cash-coin"></i> Record Income
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection