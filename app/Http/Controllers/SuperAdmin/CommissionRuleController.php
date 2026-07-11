<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CommissionRule;
use App\Models\Provider;
use Illuminate\Http\Request;

class CommissionRuleController extends Controller
{
    public function index()
    {
        $providers = Provider::where('status', 'active')->get();
        $commissionRules = CommissionRule::with('provider', 'creator')->latest()->paginate(20);
        return view('super-admin.cashpoint.commission-rules.index', compact('providers', 'commissionRules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id|unique:commission_rules,provider_id',
            'agent_percentage' => 'required|numeric|min:0|max:100',
            'system_percentage' => 'required|numeric|min:0|max:100',
        ]);

        CommissionRule::create([
            'provider_id' => $validated['provider_id'],
            'agent_percentage' => $validated['agent_percentage'],
            'system_percentage' => $validated['system_percentage'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Commission rule created successfully.');
    }

    public function update(Request $request, CommissionRule $commissionRule)
    {
        $validated = $request->validate([
            'agent_percentage' => 'required|numeric|min:0|max:100',
            'system_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $commissionRule->update($validated);

        return redirect()->back()->with('success', 'Commission rule updated successfully.');
    }

    public function destroy(CommissionRule $commissionRule)
    {
        $commissionRule->update(['status' => 'inactive']);
        return redirect()->back()->with('success', 'Commission rule deactivated.');
    }
}