<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashpointProvider;
use App\Models\CashpointSession;
use App\Models\User;
use App\Services\CashpointService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminCashpointController extends Controller
{
    protected CashpointService $cashpointService;

    public function __construct(CashpointService $cashpointService)
    {
        $this->cashpointService = $cashpointService;
    }

    /**
     * Dashboard with overall stats.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $totalStaff = User::where('role', 'staff')->count();
        $todaySessions = CashpointSession::where('session_date', $today)->count();
        $openSessions = CashpointSession::where('session_date', $today)->where('status', 'Open')->count();
        $closedSessions = CashpointSession::where('session_date', $today)->where('status', 'Closed')->count();
        $totalSessions = CashpointSession::count();
        $providers = CashpointProvider::all();

        return view('super-admin.cashpoint.dashboard', compact(
            'totalStaff', 'todaySessions', 'openSessions', 'closedSessions', 'totalSessions', 'providers'
        ));
    }

    /**
     * View all sessions with filters (DataTables).
     */
    public function allSessions(Request $request): View
    {
        $staff = User::where('role', 'staff')->orderBy('name')->get();
        return view('super-admin.cashpoint.sessions', compact('staff'));
    }

    /**
     * Get all sessions data (AJAX for DataTables).
     */
    public function getSessionsData(Request $request): JsonResponse
    {
        $query = CashpointSession::with('user');

        if ($request->filled('from')) {
            $query->where('session_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('session_date', '<=', $request->to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $sessions = $query->orderByDesc('session_date')->orderByDesc('created_at')->get();

        return response()->json(['data' => $sessions]);
    }

    /**
     * View session details.
     */
    public function show(CashpointSession $session): JsonResponse
    {
        $session->load('user');
        $summary = $this->cashpointService->getDailySummary($session);

        return response()->json([
            'success' => true,
            'session' => $session,
            'summary' => $summary,
        ]);
    }

    /**
     * Reset balances for a staff member.
     */
    public function resetBalances(Request $request, User $staff): JsonResponse
    {
        $this->cashpointService->resetOpeningBalances($staff);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'super_admin',
            'action_type' => 'cashpoint_reset',
            'reference_id' => $staff->id,
            'description' => "Super Admin reset cash point balances for {$staff->name}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Opening balances reset for {$staff->name}.",
        ]);
    }

    /**
     * Reopen a closed session.
     */
    public function reopenSession(Request $request, CashpointSession $session): JsonResponse
    {
        $this->cashpointService->reopenSession($session);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'super_admin',
            'action_type' => 'cashpoint_reopen',
            'reference_id' => $session->id,
            'description' => "Super Admin reopened session #{$session->id} for {$session->session_date}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session reopened successfully.',
        ]);
    }

    /**
     * Manage providers.
     */
    public function providers(): View
    {
        $providers = CashpointProvider::orderBy('name')->get();
        return view('super-admin.cashpoint.providers', compact('providers'));
    }

    /**
     * Store a new provider.
     */
    public function storeProvider(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:cashpoint_providers,name',
            'status' => 'required|in:active,inactive',
        ]);

        $provider = CashpointProvider::create([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'super_admin',
            'action_type' => 'cashpoint_provider_create',
            'reference_id' => $provider->id,
            'description' => "Created cash point provider: {$provider->name}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Provider created successfully.',
            'provider' => $provider,
        ]);
    }

    /**
     * Update a provider.
     */
    public function updateProvider(Request $request, CashpointProvider $provider): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:cashpoint_providers,name,' . $provider->id,
            'status' => 'required|in:active,inactive',
        ]);

        $provider->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'super_admin',
            'action_type' => 'cashpoint_provider_update',
            'reference_id' => $provider->id,
            'description' => "Updated cash point provider: {$provider->name}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Provider updated successfully.',
            'provider' => $provider->fresh(),
        ]);
    }

    /**
     * Delete a provider.
     */
    public function destroyProvider(CashpointProvider $provider): JsonResponse
    {
        $name = $provider->name;
        $provider->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'super_admin',
            'action_type' => 'cashpoint_provider_delete',
            'reference_id' => $provider->id,
            'description' => "Deleted cash point provider: {$name}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Provider deleted successfully.',
        ]);
    }

    /**
     * Audit trail for cash point operations.
     */
    public function auditLogs(): View
    {
        $logs = ActivityLog::where(function ($q) {
            $q->where('action_type', 'like', 'cashpoint_%');
        })
        ->orderByDesc('created_at')
        ->paginate(50);

        return view('super-admin.cashpoint.audit-logs', compact('logs'));
    }

    /**
     * Correction: manually adjust a session's balances.
     */
    public function correctSession(Request $request, CashpointSession $session): JsonResponse
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'value' => 'required|numeric|min:0',
        ]);

        $allowedFields = [
            'opening_cash', 'opening_mpesa_float', 'opening_airtel_float', 'opening_mixx_float', 'opening_halopesa_float',
            'closing_cash', 'closing_mpesa_float', 'closing_airtel_float', 'closing_mixx_float', 'closing_halopesa_float',
        ];

        if (!in_array($validated['field'], $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field.'], 422);
        }

        $oldValue = $session->{$validated['field']};
        $session->{$validated['field']} = $validated['value'];

        // Recalculate differences if closing field
        if (str_starts_with($validated['field'], 'closing_')) {
            $prefix = substr($validated['field'], 8); // remove 'closing_'
            $openingField = 'opening_' . $prefix;
            $diffField = match ($prefix) {
                'cash' => 'cash_difference',
                'mpesa_float' => 'mpesa_difference',
                'airtel_float' => 'airtel_difference',
                'mixx_float' => 'mixx_difference',
                'halopesa_float' => 'halopesa_difference',
                default => null,
            };
            if ($diffField) {
                $session->{$diffField} = $session->{$validated['field']} - $session->{$openingField};
            }
        }

        $session->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'super_admin',
            'action_type' => 'cashpoint_correction',
            'reference_id' => $session->id,
            'description' => "Super Admin corrected {$validated['field']} from {$oldValue} to {$validated['value']} for session #{$session->id}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session corrected successfully.',
            'session' => $session->fresh(),
        ]);
    }
}