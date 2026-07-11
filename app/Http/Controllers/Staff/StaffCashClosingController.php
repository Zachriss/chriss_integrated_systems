<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\TransactionEngine;
use App\Models\CashPoint;
use App\Models\Provider;
use App\Models\CashOpening;
use App\Models\CashClosing;
use App\Models\StaffCashAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffCashClosingController extends Controller
{
    protected TransactionEngine $engine;

    public function __construct(TransactionEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Show the closing form with balance inputs for ALL providers.
     */
    public function create()
    {
        $user = auth()->user();
        $assignment = StaffCashAssignment::active()
            ->where('staff_id', $user->id)
            ->with('cashPoint')
            ->first();

        if (!$assignment) {
            return view('staff.cashpoint.not-assigned');
        }

        $cashPoint = $assignment->cashPoint;
        $today = Carbon::today();
        $providers = Provider::where('status', 'active')->get();

        // Get today's openings and existing closings
        $openings = CashOpening::where('cash_point_id', $cashPoint->id)
            ->whereDate('opening_date', $today)
            ->get()
            ->keyBy('provider_id');

        $existingClosings = CashClosing::where('cash_point_id', $cashPoint->id)
            ->whereDate('closing_date', $today)
            ->get()
            ->keyBy('provider_id');

        // Check if any providers are already locked
        $allLocked = true;
        $providerData = [];
        foreach ($providers as $provider) {
            $closing = $existingClosings->get($provider->id);
            $isLocked = $closing && $closing->is_locked;
            if (!$isLocked) {
                $allLocked = false;
            }

            $balance = $this->engine->getTodayBalance($cashPoint->id, $provider->id);
            $providerData[$provider->id] = [
                'closing' => $closing,
                'is_locked' => $isLocked,
                'balance' => $balance,
                'opening' => $openings->get($provider->id),
            ];
        }

        if ($allLocked) {
            return redirect()->route('staff.cashpoint.dashboard')
                ->with('info', 'All providers are already closed and locked for today.');
        }

        return view('staff.cashpoint.closing', compact(
            'cashPoint', 'providers', 'providerData', 'today'
        ));
    }

    /**
     * Store closing balances for ALL providers at once.
     * ONLY accepts closing_balance per provider - NO transaction types.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $assignment = StaffCashAssignment::active()
            ->where('staff_id', $user->id)
            ->with('cashPoint')
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'You are not assigned to any cash point.');
        }

        $cashPoint = $assignment->cashPoint;
        $providers = Provider::where('status', 'active')->get();

        // Validate ONLY closing_balance for each provider
        $rules = [];
        foreach ($providers as $provider) {
            $rules["closing_balance.{$provider->id}"] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);
        $results = [];
        $hasError = false;

        foreach ($providers as $provider) {
            $closingBalance = $validated['closing_balance'][$provider->id] ?? null;

            // Skip if no balance provided (provider may not be used)
            if ($closingBalance === null) {
                continue;
            }

            // Check if already locked
            $existingClosing = CashClosing::where('cash_point_id', $cashPoint->id)
                ->where('provider_id', $provider->id)
                ->whereDate('closing_date', Carbon::today())
                ->where('is_locked', true)
                ->first();

            if ($existingClosing) {
                $results[] = "{$provider->name}: Already locked, skipped.";
                continue;
            }

            try {
                $closing = $this->engine->processClosing(
                    $cashPoint->id,
                    $provider->id,
                    (float) $closingBalance,
                    $user->id
                );
                $results[] = "{$provider->name}: ✓ Closed (Expected: " . number_format($closing->expected_balance) . ", Diff: " . number_format($closing->difference) . ")";
            } catch (\Exception $e) {
                $results[] = "{$provider->name}: ✗ Error - {$e->getMessage()}";
                $hasError = true;
            }
        }

        if ($hasError) {
            return redirect()->back()->with('warning', implode('<br>', $results));
        }

        return redirect()->route('staff.cashpoint.dashboard')
            ->with('success', implode('<br>', $results));
    }
}