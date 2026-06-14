<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ArrayExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings
{
    public function __construct(private readonly array $rows, private readonly array $headings)
    {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}

class ReportController extends Controller
{
    public function index()
    {
        return view('super-admin.reports.index');
    }

    public function users(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($roleQuery) use ($request) {
                $roleQuery->where('slug', $request->role)
                    ->orWhere('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->latest()->get();
        $roles = Role::orderBy('name')->get();

        return view('super-admin.reports.users', compact('users', 'roles'));
    }

    public function roles()
    {
        $roles = Role::withCount('users', 'permissions')->get();
        return view('super-admin.reports.roles', compact('roles'));
    }

    public function auditLogs(Request $request)
    {
        $query = AuditTrail::with('actor');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->get();
        $actions = AuditTrail::select('action')->distinct()->pluck('action');

        return view('super-admin.reports.audit-logs', compact('logs', 'actions'));
    }

    public function systemUsage()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $totalLogs = AuditTrail::count();
        $logsByModule = AuditTrail::selectRaw('module, COUNT(*) as count')
            ->whereNotNull('module')
            ->groupBy('module')
            ->pluck('count', 'module');

        $usersByRole = Role::withCount('users')
            ->orderBy('name')
            ->pluck('users_count', 'name');

        return view('super-admin.reports.system-usage', compact(
            'totalUsers', 'activeUsers', 'totalLogs', 'logsByModule', 'usersByRole'
        ));
    }

    public function export(Request $request, string $report, string $format)
    {
        [$headings, $rows, $title] = match ($report) {
            'users' => $this->usersDataset($request),
            'roles' => $this->rolesDataset(),
            'audit-logs' => $this->auditLogsDataset($request),
            'system-usage' => $this->systemUsageDataset(),
        };

        $filename = $report.'-report-'.now()->format('Y-m-d-H-i-s');

        if ($format === 'pdf') {
            return Pdf::loadView('super-admin.reports.export', compact('title', 'headings', 'rows'))
                ->setPaper('a4', 'landscape')
                ->download($filename.'.pdf');
        }

        return Excel::download(new ArrayExport($rows, $headings), $filename.'.xlsx');
    }

    private function usersDataset(Request $request): array
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($roleQuery) use ($request) {
                $roleQuery->where('slug', $request->role)
                    ->orWhere('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $rows = $query->latest()->get()->map(fn (User $user): array => [
            $user->name,
            $user->email,
            $user->roles->pluck('name')->join(', '),
            $user->status,
            optional($user->last_login_at)->format('Y-m-d H:i:s') ?? 'Never',
            $user->created_at->format('Y-m-d H:i:s'),
        ])->all();

        return [['Name', 'Email', 'Roles', 'Status', 'Last Login', 'Created At'], $rows, 'Users Report'];
    }

    private function rolesDataset(): array
    {
        $rows = Role::withCount('users', 'permissions')->get()->map(fn (Role $role): array => [
            $role->name,
            $role->slug,
            $role->description,
            $role->users_count,
            $role->permissions_count,
        ])->all();

        return [['Name', 'Slug', 'Description', 'Users', 'Permissions'], $rows, 'Roles Report'];
    }

    private function auditLogsDataset(Request $request): array
    {
        $query = AuditTrail::with('actor');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $rows = $query->latest()->get()->map(fn (AuditTrail $log): array => [
            $log->actor?->name ?? $log->actor_name ?? 'System',
            $log->action,
            $log->module,
            $log->description,
            $log->ip_address,
            $log->created_at->format('Y-m-d H:i:s'),
        ])->all();

        return [['Actor', 'Action', 'Module', 'Description', 'IP Address', 'Created At'], $rows, 'Audit Logs Report'];
    }

    private function systemUsageDataset(): array
    {
        $rows = [
            ['Total Users', User::count()],
            ['Active Users', User::where('status', 'active')->count()],
            ['Total Roles', Role::count()],
            ['Total Audit Logs', AuditTrail::count()],
        ];

        return [['Metric', 'Value'], $rows, 'System Usage Report'];
    }
}
