<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashClosing;
use App\Models\CashOpening;
use App\Models\CashPoint;
use App\Models\PaymentChannel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffCashPointController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $channels = PaymentChannel::where('status', 'active')->orderBy('name')->get();

        $cashPoint = CashPoint::whereDate('date', $today)->first();

        // Get opening balances for today
        $openings = $cashPoint
            ? CashOpening::with('paymentChannel')->where('cash_point_id', $cashPoint->id)->where('opening_date', $today)->get()
            : collect();

        // Get closing balances for today
        $closings = $cashPoint
            ? CashClosing::with('paymentChannel')->where('cash_point_id', $cashPoint->id)->where('closing_date', $today)->get()
            : collect();

        // Build per-channel data
        $channelData = $channels->map(function ($ch) use ($openings, $closings) {
            $opening = $openings->firstWhere('payment_channel_id', $ch->id);
            $closing = $closings->firstWhere('payment_channel_id', $ch->id);

            return (object) [
                'channel' => $ch,
                'opening_balance' => $opening?->opening_balance ?? 0,
                'closing_balance' => $closing?->closing_balance ?? 0,
                'difference' => $closing ? ($closing->closing_balance - ($opening?->opening_balance ?? 0)) : 0,
                'is_opened' => $opening !== null,
                'is_closed' => $closing !== null,
            ];
        });

        return view('staff.cash-point', compact('cashPoint', 'channels', 'channelData'));
    }

    public function setOpening(Request $request): JsonResponse
    {
        $staff = $request->user();
        $today = Carbon::today();

        $cashPoint = CashPoint::firstOrCreate(
            ['date' => $today],
            [
                'admin_id' => $staff->id,
                'opening_mpesa' => 0, 'opening_airtel' => 0, 'opening_tigo' => 0,
                'opening_halo' => 0, 'opening_cash' => 0,
            ]
        );

        $validated = $request->validate([
            'channel_id' => 'required|exists:payment_channels,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        // Prevent overwriting locked openings
        $existing = CashOpening::where('cash_point_id', $cashPoint->id)
            ->where('payment_channel_id', $validated['channel_id'])
            ->where('opening_date', $today)
            ->where('is_locked', true)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Opening balance already locked for this channel.'], 422);
        }

        CashOpening::updateOrCreate(
            [
                'cash_point_id' => $cashPoint->id,
                'payment_channel_id' => $validated['channel_id'],
                'opening_date' => $today,
            ],
            [
                'opening_balance' => $validated['opening_balance'],
                'created_by' => $staff->id,
                'is_locked' => true,
            ]
        );

        ActivityLog::create([
            'user_id' => $staff->id, 'role' => $staff->role,
            'action_type' => 'cash_opening',
            'reference_id' => $cashPoint->id,
            'description' => "Staff set opening: channel #{$validated['channel_id']} TZS {$validated['opening_balance']}",
            'date' => $today,
        ]);

        return response()->json(['success' => true, 'message' => 'Opening balance recorded and locked.']);
    }

    public function setClosing(Request $request): JsonResponse
    {
        $staff = $request->user();
        $today = Carbon::today();

        $cashPoint = CashPoint::whereDate('date', $today)->first();
        if (!$cashPoint) {
            return response()->json(['success' => false, 'message' => 'No cash point for today. Record opening balances first.'], 400);
        }

        $validated = $request->validate([
            'channel_id' => 'required|exists:payment_channels,id',
            'closing_balance' => 'required|numeric|min:0',
        ]);

        // Prevent editing if already closed
        $existing = CashClosing::where('cash_point_id', $cashPoint->id)
            ->where('payment_channel_id', $validated['channel_id'])
            ->where('closing_date', $today)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Closing already recorded for this channel. Cannot edit.'], 422);
        }

        // Get opening balance to calculate expected
        $opening = CashOpening::where('cash_point_id', $cashPoint->id)
            ->where('payment_channel_id', $validated['channel_id'])
            ->where('opening_date', $today)
            ->first();

        $opBal = $opening?->opening_balance ?? 0;
        $expected = $opBal; // No transactions to add - just opening = expected
        $diff = $validated['closing_balance'] - $expected;

        CashClosing::create([
            'cash_point_id' => $cashPoint->id,
            'payment_channel_id' => $validated['channel_id'],
            'closing_balance' => $validated['closing_balance'],
            'expected_balance' => $expected,
            'difference' => $diff,
            'closing_date' => $today,
            'recorded_by' => $staff->id,
        ]);

        ActivityLog::create([
            'user_id' => $staff->id, 'role' => $staff->role,
            'action_type' => 'cash_closing',
            'reference_id' => $cashPoint->id,
            'description' => "Staff closing: channel #{$validated['channel_id']} TZS {$validated['closing_balance']}",
            'date' => $today,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Closing balance recorded.',
        ]);
    }
}