<?php

namespace App\Services;

use App\Models\CashpointSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashpointService
{
    /**
     * Get or create today's session for a user.
     * If a closed session exists for yesterday, carry forward closing balances.
     */
    public function getTodaySession(User $user): CashpointSession
    {
        $today = Carbon::today();

        $session = CashpointSession::where('user_id', $user->id)
            ->where('session_date', $today)
            ->first();

        if ($session) {
            return $session;
        }

        // Check yesterday's closed session for carry forward
        $yesterday = Carbon::yesterday();
        $yesterdaySession = CashpointSession::where('user_id', $user->id)
            ->where('session_date', $yesterday)
            ->where('status', 'Closed')
            ->first();

        if ($yesterdaySession) {
            // Carry forward yesterday's closing balances
            return CashpointSession::create([
                'user_id' => $user->id,
                'session_date' => $today,
                'status' => 'Open',
                'opening_cash' => $yesterdaySession->closing_cash,
                'opening_mpesa_float' => $yesterdaySession->closing_mpesa_float,
                'opening_airtel_float' => $yesterdaySession->closing_airtel_float,
                'opening_mixx_float' => $yesterdaySession->closing_mixx_float,
                'opening_halopesa_float' => $yesterdaySession->closing_halopesa_float,
                'opened_at' => now(),
            ]);
        }

        // First day - create empty session (staff will fill opening balances)
        return CashpointSession::create([
            'user_id' => $user->id,
            'session_date' => $today,
            'status' => 'Open',
            'opened_at' => now(),
        ]);
    }

    /**
     * Open a session with initial opening balances (first day or reset).
     */
    public function openSession(User $user, array $openingBalances): CashpointSession
    {
        $today = Carbon::today();

        $session = CashpointSession::updateOrCreate(
            ['user_id' => $user->id, 'session_date' => $today],
            [
                'opening_cash' => $openingBalances['opening_cash'] ?? 0,
                'opening_mpesa_float' => $openingBalances['opening_mpesa_float'] ?? 0,
                'opening_airtel_float' => $openingBalances['opening_airtel_float'] ?? 0,
                'opening_mixx_float' => $openingBalances['opening_mixx_float'] ?? 0,
                'opening_halopesa_float' => $openingBalances['opening_halopesa_float'] ?? 0,
                'status' => 'Open',
                'opened_at' => now(),
            ]
        );

        return $session;
    }

    /**
     * Close a session by recording closing balances and calculating differences.
     */
    public function closeSession(CashpointSession $session, array $closingBalances): CashpointSession
    {
        $session->closing_cash = $closingBalances['closing_cash'] ?? 0;
        $session->closing_mpesa_float = $closingBalances['closing_mpesa_float'] ?? 0;
        $session->closing_airtel_float = $closingBalances['closing_airtel_float'] ?? 0;
        $session->closing_mixx_float = $closingBalances['closing_mixx_float'] ?? 0;
        $session->closing_halopesa_float = $closingBalances['closing_halopesa_float'] ?? 0;

        // Calculate differences
        $session->cash_difference = $session->closing_cash - $session->opening_cash;
        $session->mpesa_difference = $session->closing_mpesa_float - $session->opening_mpesa_float;
        $session->airtel_difference = $session->closing_airtel_float - $session->opening_airtel_float;
        $session->mixx_difference = $session->closing_mixx_float - $session->opening_mixx_float;
        $session->halopesa_difference = $session->closing_halopesa_float - $session->opening_halopesa_float;

        $session->status = 'Closed';
        $session->closed_at = now();
        $session->save();

        // Automatically create next day's session with carried forward balances
        $this->createNextDaySession($session);

        return $session->fresh();
    }

    /**
     * Create tomorrow's session — opening balances are NOT carried forward.
     * Staff must fill the opening form again at the start of each new day.
     */
    private function createNextDaySession(CashpointSession $closedSession): void
    {
        $tomorrow = Carbon::tomorrow();

        // Only create if tomorrow's session doesn't exist
        $existing = CashpointSession::where('user_id', $closedSession->user_id)
            ->where('session_date', $tomorrow)
            ->first();

        if (!$existing) {
            CashpointSession::create([
                'user_id' => $closedSession->user_id,
                'session_date' => $tomorrow,
                'status' => 'Open',
                'opening_cash' => 0,
                'opening_mpesa_float' => 0,
                'opening_airtel_float' => 0,
                'opening_mixx_float' => 0,
                'opening_halopesa_float' => 0,
            ]);
        }
    }

    /**
     * Check if user needs to set opening balances (first day or reset).
     */
    public function needsOpeningBalanceSetup(User $user): bool
    {
        $today = Carbon::today();
        $session = CashpointSession::where('user_id', $user->id)
            ->where('session_date', $today)
            ->first();

        // If no session today OR session has zero opening balances
        if (!$session) {
            return true;
        }

        return !$session->hasOpeningBalances();
    }

    /**
     * Get the daily summary for a specific session.
     */
    public function getDailySummary(CashpointSession $session): array
    {
        return [
            'date' => $session->session_date,
            'status' => $session->status,
            'cash' => [
                'opening' => $session->opening_cash,
                'closing' => $session->closing_cash,
                'difference' => $session->cash_difference,
            ],
            'mpesa' => [
                'opening' => $session->opening_mpesa_float,
                'closing' => $session->closing_mpesa_float,
                'difference' => $session->mpesa_difference,
            ],
            'airtel' => [
                'opening' => $session->opening_airtel_float,
                'closing' => $session->closing_airtel_float,
                'difference' => $session->airtel_difference,
            ],
            'mixx' => [
                'opening' => $session->opening_mixx_float,
                'closing' => $session->closing_mixx_float,
                'difference' => $session->mixx_difference,
            ],
            'halopesa' => [
                'opening' => $session->opening_halopesa_float,
                'closing' => $session->closing_halopesa_float,
                'difference' => $session->halopesa_difference,
            ],
        ];
    }

    /**
     * Reset opening balances for a user (admin/super admin).
     */
    public function resetOpeningBalances(User $user, Carbon $date = null): void
    {
        $date = $date ?? Carbon::today();
        $session = CashpointSession::where('user_id', $user->id)
            ->where('session_date', $date)
            ->first();

        if ($session) {
            $session->update([
                'opening_cash' => 0,
                'opening_mpesa_float' => 0,
                'opening_airtel_float' => 0,
                'opening_mixx_float' => 0,
                'opening_halopesa_float' => 0,
                'status' => 'Open',
            ]);
        }
    }

    /**
     * Reopen a closed session (admin/super admin).
     */
    public function reopenSession(CashpointSession $session): CashpointSession
    {
        $session->update([
            'status' => 'Open',
            'closed_at' => null,
            'closing_cash' => 0,
            'closing_mpesa_float' => 0,
            'closing_airtel_float' => 0,
            'closing_mixx_float' => 0,
            'closing_halopesa_float' => 0,
            'cash_difference' => 0,
            'mpesa_difference' => 0,
            'airtel_difference' => 0,
            'mixx_difference' => 0,
            'halopesa_difference' => 0,
        ]);

        return $session->fresh();
    }
}