<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderFeeRule;
use Illuminate\Http\Request;

class FeeRuleController extends Controller
{
    public function index(Request $request)
    {
        $providers = Provider::where('status', 'active')->get();

        $query = ProviderFeeRule::with('provider', 'creator');

        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $feeRules = $query->latest()->paginate(20)->withQueryString();

        if ($request->has('export')) {
            $rules = ProviderFeeRule::with('provider')
                ->when($request->filled('provider_id'), fn($q) => $q->where('provider_id', $request->provider_id))
                ->when($request->filled('transaction_type'), fn($q) => $q->where('transaction_type', $request->transaction_type))
                ->get();

            $csv = "Provider,Transaction Type,Min Amount,Max Amount,Fee Amount,Status\n";
            foreach ($rules as $rule) {
                $csv .= "{$rule->provider->name},{$rule->transaction_type},{$rule->min_amount}," . ($rule->max_amount ?? 'Unlimited') . ",{$rule->fee_amount},{$rule->status}\n";
            }

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="fee-rules-' . now()->format('Y-m-d') . '.csv"',
            ]);
        }

        $selectedProvider = $request->provider_id;
        $selectedType = $request->transaction_type;

        return view('super-admin.cashpoint.fee-rules.index', compact('providers', 'feeRules', 'selectedProvider', 'selectedType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'transaction_type' => 'required|in:deposit,withdraw',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'fee_amount' => 'required|numeric|min:0',
        ]);

        ProviderFeeRule::create([
            'provider_id' => $validated['provider_id'],
            'transaction_type' => $validated['transaction_type'],
            'min_amount' => $validated['min_amount'],
            'max_amount' => $validated['max_amount'],
            'fee_amount' => $validated['fee_amount'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Fee rule created successfully.');
    }

    public function update(Request $request, ProviderFeeRule $feeRule)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:deposit,withdraw',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'fee_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $feeRule->update($validated);

        return redirect()->back()->with('success', 'Fee rule updated successfully.');
    }

    public function destroy(ProviderFeeRule $feeRule)
    {
        $feeRule->update(['status' => 'inactive']);
        return redirect()->back()->with('success', 'Fee rule deactivated.');
    }
}