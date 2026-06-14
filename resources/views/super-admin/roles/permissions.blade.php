@extends('super-admin.layouts.super-admin')

@section('title', 'Permission Matrix')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Permission Matrix</h1>
        <p>Review role access across every ERP module.</p>
    </div>
    <a href="{{ route('super-admin.roles.index') }}" class="btn btn-sa-outline btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Roles</a>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width:260px;">Permission</th>
                        @foreach($roles as $role)
                            <th class="text-center">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $module => $modulePermissions)
                        <tr>
                            <td colspan="{{ $roles->count() + 1 }}" class="bg-light fw-semibold text-uppercase" style="font-size:0.76rem;color:#64748b;">{{ $module }}</td>
                        </tr>
                        @foreach($modulePermissions as $permission)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $permission->name }}</div>
                                    <div class="text-muted" style="font-size:0.78rem;">{{ $permission->slug }}</div>
                                </td>
                                @foreach($roles as $role)
                                    <td class="text-center">
                                        @if($role->permissions->contains('id', $permission->id))
                                            <i class="bi bi-check-circle-fill text-success" title="Allowed"></i>
                                        @else
                                            <i class="bi bi-dash-circle text-muted" title="Not allowed"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
