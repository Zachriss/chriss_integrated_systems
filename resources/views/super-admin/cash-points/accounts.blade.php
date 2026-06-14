@extends('super-admin.layouts.super-admin')

@section('title', 'Cash Point Accounts')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Cash Point Accounts</h1>
        <p>Manage and configure cash point accounts for the system.</p>
    </div>
    <button class="btn btn-sa-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal"><i class="bi bi-plus-lg me-1"></i> New Account</button>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Bank</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold" style="font-size:0.9rem;">Main Operating Account</div>
                            <div style="font-size:0.75rem;color:#94a3b8;">Primary business account</div>
                        </td>
                        <td style="font-size:0.8rem;">****1234</td>
                        <td>CRDB Bank</td>
                        <td class="fw-bold" style="color:#166534;">TSh 12,450,000.00</td>
                        <td><span class="sa-badge sa-badge-active">Active</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-sa-outline btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-sa-outline btn-outline-danger" title="Deactivate"><i class="bi bi-pause"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold" style="font-size:0.9rem;">Petty Cash</div>
                            <div style="font-size:0.75rem;color:#94a3b8;">Small expenses account</div>
                        </td>
                        <td style="font-size:0.8rem;">****5678</td>
                        <td>NMB Bank</td>
                        <td class="fw-bold" style="color:#854d0e;">TSh 450,000.00</td>
                        <td><span class="sa-badge sa-badge-active">Active</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-sa-outline btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-sa-outline btn-outline-danger" title="Deactivate"><i class="bi bi-pause"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Cash Point Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Account Name</label>
                        <input type="text" class="form-control" placeholder="e.g. Main Operating Account">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-control" placeholder="e.g. 1234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" class="form-control" placeholder="e.g. CRDB Bank">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Balance</label>
                        <input type="number" class="form-control" placeholder="0.00">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sa-primary">Save Account</button>
            </div>
        </div>
    </div>
</div>
@endsection