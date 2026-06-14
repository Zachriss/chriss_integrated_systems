<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashClosing;
use App\Models\CashOpening;
use App\Models\CashPoint;
use App\Models\CashTransaction;
use App\Models\PaymentChannel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashPointController extends Controller
{
    public function index(): View
    {
        $admin = auth()->user();
        $today = Carbon::today();
        $channels = PaymentChannel::where('status', 'active')->orderBy('name')->get();

        $cashPoint = CashPoint::where('admin_id', $admin->id)->whereDate('date', $today)->first();
        $openings = $cashPoint ? CashOpening::with('paymentChannel')->where('cash_point_id', $cashPoint->id)->where('opening_date', $today)->get() : collect();
        $closings = $cashPoint ? CashClosing::with('paymentChannel')->where('cash_point_id', $cashPoint->id)->where('closing_date', $today)->get() : collect();
        $transactions = $cashPoint ? CashTransaction::with(['paymentChannel', 'staff', 'fromChannel', 'toChannel'])->where('cash_point_id', $cashPoint->id)->orderByDesc('id')->paginate(20) : collect();

        $channelData = $channels->map(function ($ch) use ($cashPoint) {
            $incoming = $cashPoint ? CashTransaction::where('cash_point_id', $cashPoint->id)->where('payment_channel_id', $ch->id)->where('transaction_type', 'income')->sum('amount') : 0;
            $incoming += $cashPoint ? CashTransaction::where('cash_point_id', $cashPoint->id)->where('to_channel_id', $ch->id)->sum('amount') : 0;
            $outgoing = $cashPoint ? CashTransaction::where('cash_point_id', $cashPoint->id)->where('from_channel_id', $ch->id)->sum('amount') : 0;
            return (object)['channel' => $ch, 'incoming' => $incoming, 'outgoing' => $outgoing];
        });

        $cashPoints = CashPoint::where('admin_id', $admin->id)->orderByDesc('date')->paginate(10);

        return view('admin.cash-points.dashboard', compact('cashPoint', 'channels', 'openings', 'closings', 'transactions', 'channelData', 'cashPoints'));
    }

    public function create(): View
    {
        $admin = auth()->user();
        $today = Carbon::today();
        $channels = PaymentChannel::where('status', 'active')->orderBy('name')->get();
        $existingCashPoint = CashPoint::where('admin_id', $admin->id)->whereDate('date', $today)->first();
        return view('admin.cash-points.create', compact('channels', 'existingCashPoint'));
    }

    public function store(Request $request)
    {
        $admin = auth()->user();
        $today = Carbon::today();

        // Use firstOrCreate to prevent duplicate entry for same admin+date
        $cashPoint = CashPoint::firstOrCreate(
            ['admin_id' => $admin->id, 'date' => $today],
            ['opening_mpesa' => 0, 'opening_airtel' => 0, 'opening_tigo' => 0, 'opening_halo' => 0, 'opening_cash' => 0, 'notes' => $request->notes]
        );

        // If opening balance fields were submitted via old form, update them
        if ($request->has('opening_mpesa')) {
            $cashPoint->update($request->only(['opening_mpesa','opening_airtel','opening_tigo','opening_halo','opening_cash','notes']));
            // Also create per-channel opening records for backward compatibility
            $channels = PaymentChannel::where('status','active')->get();
            $mapping = ['mpesa'=>'opening_mpesa','airtel'=>'opening_airtel','tigo'=>'opening_tigo','halotel'=>'opening_halo','cash'=>'opening_cash'];
            foreach($channels as $ch){
                if(isset($mapping[$ch->code]) && $request->filled($mapping[$ch->code])){
                    CashOpening::updateOrCreate(
                        ['cash_point_id'=>$cashPoint->id,'payment_channel_id'=>$ch->id,'opening_date'=>$today],
                        ['opening_balance'=>$request->input($mapping[$ch->code]),'created_by'=>$admin->id,'is_locked'=>true]
                    );
                }
            }
        }

        return redirect()->route('admin.cash-points.show', $cashPoint->id)->with('success', 'Cash Point ready. Set opening balances per channel.');
    }

    public function show(CashPoint $cashPoint): View
    {
        $channels = PaymentChannel::where('status', 'active')->orderBy('name')->get();
        $openings = CashOpening::with('paymentChannel')->where('cash_point_id', $cashPoint->id)->get();
        $closings = CashClosing::with('paymentChannel')->where('cash_point_id', $cashPoint->id)->get();
        $transactions = CashTransaction::with(['paymentChannel', 'staff', 'fromChannel', 'toChannel'])->where('cash_point_id', $cashPoint->id)->orderByDesc('id')->paginate(20);

        $channelData = $channels->map(function ($ch) use ($cashPoint) {
            $opening = CashOpening::where('cash_point_id', $cashPoint->id)->where('payment_channel_id', $ch->id)->first();
            $closing = CashClosing::where('cash_point_id', $cashPoint->id)->where('payment_channel_id', $ch->id)->first();
            $incoming = CashTransaction::where('cash_point_id', $cashPoint->id)->where('payment_channel_id', $ch->id)->where('transaction_type', 'income')->sum('amount');
            $incoming += CashTransaction::where('cash_point_id', $cashPoint->id)->where('to_channel_id', $ch->id)->sum('amount');
            $outgoing = CashTransaction::where('cash_point_id', $cashPoint->id)->where('from_channel_id', $ch->id)->sum('amount');
            $opBal = $opening?->opening_balance ?? 0;
            $expected = $opBal + $incoming - $outgoing;
            $clBal = $closing?->closing_balance ?? 0;
            return (object)['channel' => $ch, 'opening' => $opBal, 'incoming' => $incoming, 'outgoing' => $outgoing, 'closing' => $clBal, 'expected' => $expected, 'difference' => $clBal - $expected];
        });

        $staff = User::where('role', 'staff')->orderBy('name')->get();
        return view('admin.cash-points.show', compact('cashPoint', 'channels', 'openings', 'closings', 'transactions', 'channelData', 'staff'));
    }

    public function setOpening(Request $request, CashPoint $cashPoint): JsonResponse
    {
        $validated = $request->validate(['channel_id' => 'required|exists:payment_channels,id', 'amount' => 'required|numeric|min:0']);
        CashOpening::updateOrCreate(['cash_point_id' => $cashPoint->id, 'payment_channel_id' => $validated['channel_id'], 'opening_date' => $cashPoint->date], ['opening_balance' => $validated['amount'], 'created_by' => auth()->id(), 'is_locked' => true]);
        return response()->json(['success' => true, 'message' => 'Opening balance set.']);
    }

    public function addTransaction(Request $request, CashPoint $cashPoint): JsonResponse
    {
        $validated = $request->validate([
            'payment_channel_id' => 'required|exists:payment_channels,id',
            'transaction_type' => 'required|in:income,transfer,adjustment',
            'from_channel_id' => 'nullable|exists:payment_channels,id|different:payment_channel_id',
            'to_channel_id' => 'nullable|exists:payment_channels,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:50',
        ]);

        CashTransaction::create([
            'cash_point_id' => $cashPoint->id, 'staff_id' => auth()->id(), 'payment_channel_id' => $validated['payment_channel_id'],
            'transaction_type' => $validated['transaction_type'], 'from_channel_id' => $validated['from_channel_id'] ?? null, 'to_channel_id' => $validated['to_channel_id'] ?? null,
            'amount' => $validated['amount'], 'description' => $validated['description'] ?? null, 'reference_number' => $validated['reference_number'] ?? null,
            'type' => 'income', 'payment_method' => 'cash', 'created_by' => auth()->id(), 'transaction_date' => today(),
        ]);

        ActivityLog::create(['user_id' => auth()->id(), 'role' => auth()->user()->role, 'action_type' => 'cash_transaction', 'reference_id' => $cashPoint->id, 'description' => "Transaction: {$validated['transaction_type']} TZS {$validated['amount']}", 'date' => today()]);

        return response()->json(['success' => true, 'message' => 'Transaction recorded.']);
    }

    public function setClosing(Request $request, CashPoint $cashPoint): JsonResponse
    {
        $validated = $request->validate(['channel_id' => 'required|exists:payment_channels,id', 'closing_balance' => 'required|numeric|min:0']);

        $opening = CashOpening::where('cash_point_id', $cashPoint->id)->where('payment_channel_id', $validated['channel_id'])->first();
        $incoming = CashTransaction::where('cash_point_id', $cashPoint->id)->where('payment_channel_id', $validated['channel_id'])->where('transaction_type', 'income')->sum('amount');
        $incoming += CashTransaction::where('cash_point_id', $cashPoint->id)->where('to_channel_id', $validated['channel_id'])->sum('amount');
        $outgoing = CashTransaction::where('cash_point_id', $cashPoint->id)->where('from_channel_id', $validated['channel_id'])->sum('amount');
        $expected = ($opening?->opening_balance ?? 0) + $incoming - $outgoing;
        $diff = $validated['closing_balance'] - $expected;

        CashClosing::updateOrCreate(['cash_point_id' => $cashPoint->id, 'payment_channel_id' => $validated['channel_id'], 'closing_date' => $cashPoint->date], ['closing_balance' => $validated['closing_balance'], 'expected_balance' => $expected, 'difference' => $diff, 'recorded_by' => auth()->id()]);

        ActivityLog::create(['user_id' => auth()->id(), 'role' => auth()->user()->role, 'action_type' => 'cash_closing', 'reference_id' => $cashPoint->id, 'description' => "Closing: channel #{$validated['channel_id']} TZS {$validated['closing_balance']} (diff: {$diff})", 'date' => today()]);

        return response()->json(['success' => true, 'message' => 'Closing recorded.', 'data' => ['expected' => $expected, 'difference' => $diff]]);
    }

    public function destroy(CashPoint $cashPoint)
    {
        $cashPoint->delete();
        return redirect()->route('admin.cash-points.index')->with('success', 'Cash Point deleted.');
    }
}