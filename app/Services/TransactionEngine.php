<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\ProviderFeeRule;
use App\Models\CommissionRule;
use App\Models\CashTransaction;
use App\Models\CashOpening;
use App\Models\CashClosing;
use App\Models\DailyProfitSummary;
use App\Models\CashPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionEngine
{
    const CASH_PROVIDER_CODE = 'CASH';

    /**
     * Get the Cash provider ID.
     */
    public function getCashProviderId(): ?int
    {
        $cash = Provider::where('code', self::CASH_PROVIDER_CODE)->first();
        return $cash?->id;
    }

    /**
     * Check if a provider is the Cash provider.
     */
    public function isCashProvider(int $providerId): bool
    {
        $cashId = $this->getCashProviderId();
        return $cashId !== null && $cashId === $providerId;
    }

    /**
     * Calculate fee for a transaction based on provider fee rules.
     */
    public function calculateFee(int $providerId, string $transactionType, float $amount): float
    {
        // Cash provider has no fees
        if ($this->isCashProvider($providerId)) {
            return 0;
        }

        $rule = ProviderFeeRule::active()
            ->forProvider($providerId)
            ->forType($transactionType)
            ->where('min_amount', '<=', $amount)
            ->where(function ($q) use ($amount) {
                $q->where('max_amount', '>=', $amount)
                  ->orWhereNull('max_amount');
            })
            ->first();

        if (!$rule) {
            throw new \Exception("No fee rule found for provider {$providerId}, type {$transactionType}, amount {$amount}");
        }

        return (float) $rule->fee_amount;
    }

    /**
     * Calculate commission split from fee.
     */
    public function calculateCommission(int $providerId, float $fee): array
    {
        // Cash provider has no commission
        if ($this->isCashProvider($providerId)) {
            return [
                'agent_commission' => 0,
                'system_commission' => 0,
            ];
        }

        $rule = CommissionRule::active()
            ->forProvider($providerId)
            ->first();

        if (!$rule) {
            throw new \Exception("No commission rule found for provider {$providerId}");
        }

        $agentPercentage = (float) $rule->agent_percentage;
        $systemPercentage = (float) $rule->system_percentage;

        return [
            'agent_commission' => round($fee * ($agentPercentage / 100), 2),
            'system_commission' => round($fee * ($systemPercentage / 100), 2),
        ];
    }

    /**
     * Process a transaction: calculate fee + commission, save it.
     * For non-Cash providers, also creates inverse Cash transaction.
     */
    public function processTransaction(
        int $cashPointId,
        int $providerId,
        int $staffId,
        string $transactionType,
        float $amount,
        ?string $referenceNumber = null,
        ?string $transactionDate = null
    ): CashTransaction {
        $date = $transactionDate ? Carbon::parse($transactionDate) : Carbon::today();

        // Calculate fee based on provider fee rules
        $fee = $this->calculateFee($providerId, $transactionType, $amount);

        // Calculate commission split
        $commission = $this->calculateCommission($providerId, $fee);

        // Create the transaction for the provider
        $transaction = CashTransaction::create([
            'cash_point_id' => $cashPointId,
            'provider_id' => $providerId,
            'staff_id' => $staffId,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'fee' => $fee,
            'agent_commission' => $commission['agent_commission'],
            'system_commission' => $commission['system_commission'],
            'reference_number' => $referenceNumber ?? $this->generateReference(),
            'transaction_date' => $date,
        ]);

        // If this is NOT the Cash provider, create inverse Cash transaction
        // Deposit → Cash INCREASES, Withdraw → Cash DECREASES
        if (!$this->isCashProvider($providerId)) {
            $cashProviderId = $this->getCashProviderId();
            if ($cashProviderId) {
                $cashTransactionType = ($transactionType === 'deposit') ? 'deposit' : 'withdraw';

                CashTransaction::create([
                    'cash_point_id' => $cashPointId,
                    'provider_id' => $cashProviderId,
                    'staff_id' => $staffId,
                    'transaction_type' => $cashTransactionType,
                    'amount' => $amount,
                    'fee' => 0,
                    'agent_commission' => 0,
                    'system_commission' => 0,
                    'reference_number' => $referenceNumber ? 'CASH-' . $referenceNumber : $this->generateReference(),
                    'transaction_date' => $date,
                ]);
            }
        }

        // Update or create daily profit summary
        $this->updateDailyProfitSummary($providerId, $date);

        return $transaction;
    }

    /**
     * Process daily closing for a specific cash point and provider.
     * For Cash provider: expected = opening + all deposits - all withdrawals
     * For other providers: expected = opening + own deposits - own withdrawals
     */
    public function processClosing(
        int $cashPointId,
        int $providerId,
        float $closingBalance,
        int $recordedBy,
        ?string $closingDate = null
    ): CashClosing {
        $date = $closingDate ? Carbon::parse($closingDate) : Carbon::today();

        // Get opening balance
        $opening = CashOpening::where('cash_point_id', $cashPointId)
            ->where('provider_id', $providerId)
            ->where('opening_date', $date)
            ->first();

        $openingBalance = $opening ? (float) $opening->opening_balance : 0;

        if ($this->isCashProvider($providerId)) {
            // Cash: expected = opening + ALL deposits - ALL withdrawals (across ALL providers)
            $allDeposits = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('transaction_date', $date)
                ->where('transaction_type', 'deposit')
                ->sum('amount');

            $allWithdrawals = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('transaction_date', $date)
                ->where('transaction_type', 'withdraw')
                ->sum('amount');

            $expectedBalance = $openingBalance + $allDeposits - $allWithdrawals;
        } else {
            // Regular provider: expected = opening + own deposits - own withdrawals
            $deposits = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('provider_id', $providerId)
                ->where('transaction_date', $date)
                ->where('transaction_type', 'deposit')
                ->sum('amount');

            $withdrawals = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('provider_id', $providerId)
                ->where('transaction_date', $date)
                ->where('transaction_type', 'withdraw')
                ->sum('amount');

            $expectedBalance = $openingBalance + $deposits - $withdrawals;
        }

        $difference = $closingBalance - $expectedBalance;

        // Lock the opening record
        if ($opening && !$opening->is_locked) {
            $opening->update(['is_locked' => true]);
        }

        // Create or update closing
        $closing = CashClosing::updateOrCreate(
            [
                'cash_point_id' => $cashPointId,
                'provider_id' => $providerId,
                'closing_date' => $date,
            ],
            [
                'closing_balance' => $closingBalance,
                'expected_balance' => $expectedBalance,
                'difference' => $difference,
                'recorded_by' => $recordedBy,
                'is_locked' => true,
            ]
        );

        // Auto-generate next day's opening from this closing
        $this->generateNextDayOpening($cashPointId, $providerId, $closingBalance, $date);

        return $closing;
    }

    /**
     * Generate next day opening balance from today's closing.
     */
    public function generateNextDayOpening(int $cashPointId, int $providerId, float $closingBalance, Carbon $todayDate): void
    {
        $nextDate = $todayDate->copy()->addDay();

        CashOpening::updateOrCreate(
            [
                'cash_point_id' => $cashPointId,
                'provider_id' => $providerId,
                'opening_date' => $nextDate,
            ],
            [
                'opening_balance' => $closingBalance,
                'is_locked' => false,
                'created_by' => 1, // System
            ]
        );
    }

    /**
     * Update or create daily profit summary for a provider.
     */
    public function updateDailyProfitSummary(int $providerId, Carbon $date): void
    {
        // Skip Cash provider - no profit tracking needed
        if ($this->isCashProvider($providerId)) {
            return;
        }

        $transactions = CashTransaction::where('provider_id', $providerId)
            ->where('transaction_date', $date)
            ->get();

        $totalTransactions = $transactions->count();
        $totalFees = $transactions->sum('fee');
        $agentProfit = $transactions->sum('agent_commission');
        $systemProfit = $transactions->sum('system_commission');

        DailyProfitSummary::updateOrCreate(
            [
                'provider_id' => $providerId,
                'report_date' => $date,
            ],
            [
                'total_transactions' => $totalTransactions,
                'total_fees' => $totalFees,
                'agent_profit' => $agentProfit,
                'system_profit' => $systemProfit,
            ]
        );
    }

    /**
     * Generate a unique reference number.
     */
    private function generateReference(): string
    {
        return 'TXN-' . strtoupper(uniqid());
    }

    /**
     * Get today's balance for a cash point per provider.
     * For Cash: current = opening + ALL deposits - ALL withdrawals
     * For others: current = opening + own deposits - own withdrawals
     */
    public function getTodayBalance(int $cashPointId, int $providerId): array
    {
        $today = Carbon::today();

        $opening = CashOpening::where('cash_point_id', $cashPointId)
            ->where('provider_id', $providerId)
            ->where('opening_date', $today)
            ->first();

        $openingBalance = $opening ? (float) $opening->opening_balance : 0;

        if ($this->isCashProvider($providerId)) {
            // Cash: sum ALL deposits and withdrawals across ALL providers
            $deposits = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('transaction_date', $today)
                ->where('transaction_type', 'deposit')
                ->sum('amount');

            $withdrawals = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('transaction_date', $today)
                ->where('transaction_type', 'withdraw')
                ->sum('amount');
        } else {
            // Regular provider: own transactions only
            $deposits = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('provider_id', $providerId)
                ->where('transaction_date', $today)
                ->where('transaction_type', 'deposit')
                ->sum('amount');

            $withdrawals = (float) CashTransaction::where('cash_point_id', $cashPointId)
                ->where('provider_id', $providerId)
                ->where('transaction_date', $today)
                ->where('transaction_type', 'withdraw')
                ->sum('amount');
        }

        $closing = CashClosing::where('cash_point_id', $cashPointId)
            ->where('provider_id', $providerId)
            ->where('closing_date', $today)
            ->first();

        return [
            'opening_balance' => $openingBalance,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
            'current_balance' => $openingBalance + $deposits - $withdrawals,
            'is_cash' => $this->isCashProvider($providerId),
            'closing' => $closing ? [
                'balance' => (float) $closing->closing_balance,
                'expected' => (float) $closing->expected_balance,
                'difference' => (float) $closing->difference,
                'is_locked' => $closing->is_locked,
            ] : null,
        ];
    }
}