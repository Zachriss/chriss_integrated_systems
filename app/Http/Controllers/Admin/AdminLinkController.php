<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLinkController extends Controller
{
    public function index(): View
    {
        $links = Link::orderBy('group')->orderBy('order')->orderBy('name')->get();
        return view('admin.links.index', compact('links'));
    }

    public function create(): View
    {
        return view('admin.links.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'group' => 'required|string|in:quick_links,services,footer',
            'order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
        ]);

        Link::create($validated);

        return redirect()->route('admin.links.index')->with('success', 'Link created successfully.');
    }

    public function edit(Link $link): View
    {
        return view('admin.links.edit', compact('link'));
    }

    public function update(Request $request, Link $link): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'group' => 'required|string|in:quick_links,services,footer',
            'order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
        ]);

        $link->update($validated);

        return redirect()->route('admin.links.index')->with('success', 'Link updated successfully.');
    }

    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();
        return redirect()->route('admin.links.index')->with('success', 'Link deleted successfully.');
    }

    public function toggleStatus(Link $link): RedirectResponse
    {
        $link->update(['is_active' => !$link->is_active]);
        return back()->with('success', 'Link status updated.');
    }
}