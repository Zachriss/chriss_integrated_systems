@extends('super-admin.layouts.super-admin')

@section('title', 'User Alerts')

@section('content')
<div class="sa-page-header">
    <h1>User Alerts</h1>
    <p>Configure and send alerts to specific users or groups.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Recipient</label>
                    <select class="form-select">
                        <option value="">All Users</option>
                        <option value="admin">Administrators</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customers</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Priority</label>
                    <select class="form-select">
                        <option>Low</option>
                        <option selected>Normal</option>
                        <option>High</option>
                        <option>Critical</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" placeholder="Alert title...">
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" rows="4" placeholder="Type your alert message..."></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-send me-1"></i> Send Alert</button>
            </div>
        </form>
    </div>
</div>
@endsection