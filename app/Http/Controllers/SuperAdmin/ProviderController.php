<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::with('creator')->latest()->paginate(15);
        return view('super-admin.cashpoint.providers.index', compact('providers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:providers,code',
        ]);

        Provider::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Provider registered successfully.');
    }

    public function update(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:providers,code,' . $provider->id,
            'status' => 'required|in:active,inactive',
        ]);

        $provider->update($validated);

        return redirect()->back()->with('success', 'Provider updated successfully.');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->back()->with('success', 'Provider deleted permanently.');
    }

    public function toggleStatus(Provider $provider)
    {
        $provider->update([
            'status' => $provider->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->back()->with('success', 'Provider status updated.');
    }
}