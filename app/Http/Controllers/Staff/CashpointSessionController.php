<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashpointSession;
use App\Services\CashpointService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashpointSessionController extends Controller
{
    protected CashpointService $cashpointService;

    public function __construct(CashpointService $cashpointService)
    {
        $this->cashpointService = $cashpointService;
    }

    /**
     * Display the Cash Point dashboard for staff.
     */
    public function index(): View
    {
        $user = auth()->user();
        $today = Carbon::today();

        $session = $this->cashpointService->getTodaySession($user);
        $needsSetup = $this->cashpointService->needsOpeningBalanceSetup($user);

        // Get recent sessions (last 30 days)
        $recentSessions = CashpointSession::where('user_id', $user->id)
            ->where('session_date', '>=', $today->copy()->subDays(30))
            ->orderByDesc('session_date')
            ->get();

        $summary = $session->hasOpeningBalances()
            ? $this->cashpointService->getDailySummary($session)
            : null;

        return view('staff.cashpoint.dashboard', compact(
            'session', 'needsSetup', 'recentSessions', 'summary'
        ));
    }

    /**
     * Save opening balances (first time setup or reset).
     */
    public function storeOpening(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'opening_mpesa_float' => 'required|numeric|min:0',
            'opening_airtel_float' => 'required|numeric|min:0',
            'opening_mixx_float' => 'required|numeric|min:0',
            'opening_halopesa_float' => 'required|numeric|min:0',
        ]);

        $user = $request->user();
        $session = $this->cashpointService->openSession($user, $validated);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'cashpoint_opening',
            'reference_id' => $session->id,
            'description' => "Cash Point opened: Cash TZS {$validated['opening_cash']}, M-Pesa {$validated['opening_mpesa_float']}, Airtel {$validated['opening_airtel_float']}, Mixx {$validated['opening_mixx_float']}, HaloPesa {$validated['opening_halopesa_float']}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session opened successfully! Opening balances saved.',
            'session' => $session->fresh(),
        ]);
    }

    /**
     * Close the session with closing balances.
     */
    public function storeClosing(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $session = CashpointSession::where('user_id', $user->id)
            ->where('session_date', $today)
            ->where('status', 'Open')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'No open session found for today.'
            ], 400);
        }

        if (!$session->hasOpeningBalances()) {
            return response()->json([
                'success' => false,
                'message' => 'Please set opening balances first before closing.'
            ], 400);
        }

        $validated = $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'closing_mpesa_float' => 'required|numeric|min:0',
            'closing_airtel_float' => 'required|numeric|min:0',
            'closing_mixx_float' => 'required|numeric|min:0',
            'closing_halopesa_float' => 'required|numeric|min:0',
        ]);

        $closed = $this->cashpointService->closeSession($session, $validated);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'cashpoint_closing',
            'reference_id' => $closed->id,
            'description' => "Cash Point closed. Cash Diff: {$closed->cash_difference}, M-Pesa Diff: {$closed->mpesa_difference}, Airtel Diff: {$closed->airtel_difference}, Mixx Diff: {$closed->mixx_difference}, HaloPesa Diff: {$closed->halopesa_difference}",
            'date' => today(),
        ]);

        $summary = $this->cashpointService->getDailySummary($closed);

        return response()->json([
            'success' => true,
            'message' => 'Session closed successfully! Balances calculated.',
            'session' => $closed,
            'summary' => $summary,
        ]);
    }

    /**
     * Get session data via AJAX for dashboard refresh.
     */
    public function getSessionData(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $session = CashpointSession::where('user_id', $user->id)
            ->where('session_date', $today)
            ->first();

        if (!$session) {
            return response()->json(['session' => null, 'needsSetup' => true]);
        }

        $summary = $session->hasOpeningBalances()
            ? $this->cashpointService->getDailySummary($session)
            : null;

        return response()->json([
            'session' => $session,
            'summary' => $summary,
            'needsSetup' => !$session->hasOpeningBalances(),
            'isClosed' => $session->status === 'Closed',
        ]);
    }

    /**
     * View a specific session summary.
     */
    public function showSession(CashpointSession $session): JsonResponse
    {
        $this->authorize('view', $session);
        $summary = $this->cashpointService->getDailySummary($session);

        return response()->json([
            'success' => true,
            'session' => $session,
            'summary' => $summary,
        ]);
    }

    /**
     * Get session history for the staff user.
     */
    public function history(): View
    {
        $user = auth()->user();
        $sessions = CashpointSession::where('user_id', $user->id)
            ->orderByDesc('session_date')
            ->paginate(20);

        return view('staff.cashpoint.history', compact('sessions'));
    }
}