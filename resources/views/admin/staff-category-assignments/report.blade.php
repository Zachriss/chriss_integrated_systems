@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-0">Assignment Report</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Assigned Staff</th>
                                    <th>Total Services</th>
                                    <th>Status</th>
                                    <th>Assigned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $item['category'] }}</strong></td>
                                    <td>{{ $item['staff_name'] }}</td>
                                    <td><span class="badge bg-primary">{{ $item['total_services'] }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $item['status'] === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($item['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $item['assigned_at'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No assignments found.</td>
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
@endsection