@extends('layouts.chrissDashboardLayout')
@section('title', 'Cash Point - Not Assigned')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Cash Point Assigned</h4>
                    <p class="text-muted">You have not been assigned to any cash point yet. Please contact your administrator.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection