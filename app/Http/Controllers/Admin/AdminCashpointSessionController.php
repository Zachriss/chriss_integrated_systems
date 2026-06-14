<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashpointSession;
use App\Models\User;
use App\Services\CashpointService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCashpointSessionController extends Controller
{
    protected CashpointService $cashpointService;

    public function __construct(CashpointService $cashpointService)
    {
        $this->cashpointService = $cashpointService;
    }

    /**
     * Display all staff cash point sessions.
     */
    public function index(): View
    {
        $today = Carbon::today();

        $staff = User::where('role', 'staff')
            ->with(['cashpointSessions' => function ($q) use ($today) {
                $q->where('session_date', $today);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $session = $user->cashpointSessions->first();
                $user->today_session = $session;
                $user->session_status = $session ? $session->status : 'No Session';
                return $user;
            });

        return view('admin.cashpoint.index', compact('staff'));
    }

    /**
     * View all sessions for a specific staff member.
     */
    public function staffSessions(User $staff): View
    {
        $sessions = CashpointSession::where('user_id', $staff->id)
            ->orderByDesc('session_date')
            ->paginate(30);

        return view('admin.cashpoint.staff-sessions', compact('staff', 'sessions'));
    }

    /**
     * View a specific session details.
     */
    public function show(CashpointSession $session): JsonResponse
    {
        $summary = $this->cashpointService->getDailySummary($session);

        return response()->json([
            'success' => true,
            'session' => $session->load('user'),
            'summary' => $summary,
        ]);
    }

    /**
     * All sessions dashboard with DataTables.
     */
    public function allSessions(): View
    {
        $today = Carbon::today();
        $todaySessions = CashpointSession::with('user')
            ->where('session_date', $today)
            ->orderByDesc('created_at')
            ->get();

        $openCount = $todaySessions->where('status', 'Open')->count();
        $closedCount = $todaySessions->where('status', 'Closed')->count();

        $recentSessions = CashpointSession::with('user')
            ->orderByDesc('session_date')
            ->take(50)
            ->get();

        return view('admin.cashpoint.all-sessions', compact('todaySessions', 'openCount', 'closedCount', 'recentSessions'));
    }

    /**
     * Reset a staff member's opening balances.
     */
    public function resetBalances(Request $request, User $staff): JsonResponse
    {
        $this->cashpointService->resetOpeningBalances($staff);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => 'admin',
            'action_type' => 'cashpoint_reset',
            'reference_id' => $staff->id,
            'description' => "Admin reset cash point balances for {$staff->name}",
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
            'role' => 'admin',
            'action_type' => 'cashpoint_reopen',
            'reference_id' => $session->id,
            'description' => "Admin reopened cash point session #{$session->id} for {$session->session_date}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session reopened successfully.',
        ]);
    }

    /**
     * Get sessions data for DataTables.
     */
    public function getSessionsData(Request $request): JsonResponse
    {
        $query = CashpointSession::with('user');

        // Filter by date range
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
}