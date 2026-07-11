<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CashPoint;
use App\Models\CashOpening;
use App\Models\CashClosing;
use App\Models\CashTransaction;
use App\Models\StaffCashAssignment;
use App\Models\DailyProfitSummary;
use App\Models\User;
use Illuminate\Http\Request;

class CashPointManagementController extends Controller
{
    public function index()
    {
        $cashPoints = CashPoint::withCount('staffAssignments')->latest()->paginate(10);
        return view('super-admin.cashpoint.management.index', compact('cashPoints'));
    }

    public function storeCashPoint(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cash_points,name',
        ]);

        CashPoint::create([
            'name' => $validated['name'],
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Cash Point created successfully.');
    }

    public function updateCashPoint(Request $request, CashPoint $cashPoint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cash_points,name,' . $cashPoint->id,
            'status' => 'required|in:active,inactive',
        ]);

        $cashPoint->update($validated);
        return redirect()->back()->with('success', 'Cash Point updated successfully.');
    }

    public function assignStaff(Request $request)
    {
        $validated = $request->validate([
            'cash_point_id' => 'required|exists:cash_points,id',
            'staff_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
        ]);

        StaffCashAssignment::create([
            'cash_point_id' => $validated['cash_point_id'],
            'staff_id' => $validated['staff_id'],
            'assigned_by' => auth()->id(),
            'start_date' => $validated['start_date'],
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Staff assigned to Cash Point successfully.');
    }

    public function destroyCashPoint(CashPoint $cashPoint)
    {
        $cashPoint->delete();
        return redirect()->back()->with('success', 'Cash Point deleted permanently.');
    }

    public function endAssignment(StaffCashAssignment $assignment)
    {
        $assignment->update([
            'status' => 'ended',
            'end_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Staff assignment ended.');
    }

    public function unlockOpening(CashOpening $opening)
    {
        $opening->update(['is_locked' => false]);
        return redirect()->back()->with('success', 'Opening balance unlocked.');
    }

    public function unlockClosing(CashClosing $closing)
    {
        $closing->update(['is_locked' => false]);
        return redirect()->back()->with('success', 'Closing balance unlocked.');
    }

    public function reports()
    {
        $profitSummaries = DailyProfitSummary::with('provider')
            ->latest('report_date')
            ->paginate(20);

        return view('super-admin.cashpoint.reports.index', compact('profitSummaries'));
    }
}