<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTrail::with('actor')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25);
        $modules = AuditTrail::select('module')->distinct()->whereNotNull('module')->pluck('module');
        $actions = AuditTrail::select('action')->distinct()->whereNotNull('action')->pluck('action');

        return view('super-admin.audit-logs.index', compact('logs', 'modules', 'actions'));
    }

    public function show(AuditTrail $auditTrail)
    {
        $auditTrail->load('actor');
        return view('super-admin.audit-logs.show', ['log' => $auditTrail]);
    }
}