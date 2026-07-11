<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashPoint;
use App\Models\Provider;
use App\Models\CashOpening;
use App\Models\StaffCashAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashOpeningController extends Controller
{
    public function index()
    {
        $cashPoints = CashPoint::where('status', 'active')->get();
        $providers = Provider::where('status', 'active')->get();
        $openings = CashOpening::with('cashPoint', 'provider', 'createdBy')
            ->whereDate('opening_date', Carbon::today())
            ->get();

        return view('admin.cashpoint.openings.index', compact('cashPoints', 'providers', 'openings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cash_point_id' => 'required|exists:cash_points,id',
            'provider_id' => 'required|exists:providers,id',
            'opening_balance' => 'required|numeric|min:0',
            'opening_date' => 'required|date',
        ]);

        CashOpening::updateOrCreate(
            [
                'cash_point_id' => $validated['cash_point_id'],
                'provider_id' => $validated['provider_id'],
                'opening_date' => $validated['opening_date'],
            ],
            [
                'opening_balance' => $validated['opening_balance'],
                'is_locked' => false,
                'created_by' => auth()->id(),
            ]
        );

        return redirect()->back()->with('success', 'Opening balance set successfully.');
    }

    public function lock(CashOpening $opening)
    {
        if ($opening->is_locked) {
            return redirect()->back()->with('error', 'Opening balance is already locked.');
        }

        $opening->update(['is_locked' => true]);
        return redirect()->back()->with('success', 'Opening balance locked successfully.');
    }

    public function unlock(CashOpening $opening)
    {
        // Only super admin can unlock
        if (!auth()->user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Only Super Admin can unlock records.');
        }

        $opening->update(['is_locked' => false]);
        return redirect()->back()->with('success', 'Opening balance unlocked successfully.');
    }
}