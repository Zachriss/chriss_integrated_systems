@extends('super-admin.layouts.super-admin')

@section('title', 'Backup & Restore')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Backup & Restore</h1>
        <p>Create database backups and restore from previous backups.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sa-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
            <i class="bi bi-cloud-arrow-down me-1"></i> Create Backup
        </button>
    </div>
</div>

<div class="sa-card mb-4">
    <div class="sa-card-body p-0">
        <div class="p-3 border-bottom bg-light">
            <h5 class="mb-0 fw-bold" style="font-size:0.95rem;">SQL Backups</h5>
        </div>
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead>
                    <tr><th>File Name</th><th>Size</th><th>Created By</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($sqlBackups as $backup)
                    <tr>
                        <td><code>{{ $backup->file_name }}</code></td>
                        <td>{{ $backup->file_size }}</td>
                        <td>{{ $backup->createdBy?->name ?? '—' }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $backup->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('super-admin.backups.download', $backup) }}" class="btn btn-sm btn-sa-outline btn-outline-primary" title="Download"><i class="bi bi-download"></i></a>
                                <form action="{{ route('super-admin.backups.restore', $backup) }}" method="POST" class="d-inline" onsubmit="return confirm('WARNING: Restoring this SQL backup will overwrite current database data. Continue?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-warning" title="Restore"><i class="bi bi-arrow-clockwise"></i></button>
                                </form>
                                <form action="{{ route('super-admin.backups.destroy', $backup) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup file?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No SQL backups created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="p-3 border-bottom bg-light">
            <h5 class="mb-0 fw-bold" style="font-size:0.95rem;">ZIP Backups (Full System)</h5>
        </div>
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead>
                    <tr><th>File Name</th><th>Size</th><th>Created By</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($zipBackups as $backup)
                    <tr>
                        <td><code>{{ $backup->file_name }}</code></td>
                        <td>{{ $backup->file_size }}</td>
                        <td>{{ $backup->createdBy?->name ?? '—' }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $backup->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('super-admin.backups.download', $backup) }}" class="btn btn-sm btn-sa-outline btn-outline-primary" title="Download"><i class="bi bi-download"></i></a>
                                <form action="{{ route('super-admin.backups.restore', $backup) }}" method="POST" class="d-inline" onsubmit="return confirm('WARNING: Restoring this ZIP backup will overwrite current database and files. Continue?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-warning" title="Restore"><i class="bi bi-arrow-clockwise"></i></button>
                                </form>
                                <form action="{{ route('super-admin.backups.destroy', $backup) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup file?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No ZIP backups created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" aria-labelledby="createBackupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('super-admin.backups.create') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createBackupModalLabel">Create Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Backup Type</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="sqlBackup" value="sql" checked>
                        <label class="form-check-label" for="sqlBackup">
                            <strong>SQL Backup</strong>
                            <div class="text-muted small">Database only - smaller file, faster</div>
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="type" id="zipBackup" value="zip">
                        <label class="form-check-label" for="zipBackup">
                            <strong>ZIP Backup (Full System)</strong>
                            <div class="text-muted small">Database + files - larger file, complete restore</div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sa-outline btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-cloud-arrow-down me-1"></i> Create Backup</button>
            </div>
        </form>
    </div>
</div>
@endsection