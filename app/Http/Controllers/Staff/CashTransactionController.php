<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\TransactionEngine;
use App\Models\CashPoint;
use App\Models\Provider;
use App\Models\CashOpening;
use App\Models\CashTransaction;
use App\Models\CashClosing;
use App\Models\StaffCashAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashTransactionController extends Controller
{
    protected TransactionEngine $engine;

    public function __construct(TransactionEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * READ-ONLY Dashboard - View today's balances per provider.
     * NO deposit/withdraw buttons.
     * NO transaction modals.
     */
    public function dashboard()
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
        $providers = Provider::where('status', 'active')->get();

        $balances = [];
        foreach ($providers as $provider) {
            $balances[$provider->id] = $this->engine->getTodayBalance($cashPoint->id, $provider->id);
        }

        $today = Carbon::today();
        $openings = CashOpening::where('cash_point_id', $cashPoint->id)
            ->whereDate('opening_date', $today)
            ->get()
            ->keyBy('provider_id');

        $closings = CashClosing::where('cash_point_id', $cashPoint->id)
            ->whereDate('closing_date', $today)
            ->get()
            ->keyBy('provider_id');

        return view('staff.cashpoint.dashboard', compact(
            'cashPoint', 'providers', 'balances', 'openings', 'closings', 'today'
        ));
    }

    /**
     * READ-ONLY - View transaction history from database.
     */
    public function history()
    {
        $user = auth()->user();
        $transactions = CashTransaction::where('staff_id', $user->id)
            ->with('cashPoint', 'provider')
            ->latest()
            ->paginate(20);

        return view('staff.cashpoint.history', compact('transactions'));
    }
}