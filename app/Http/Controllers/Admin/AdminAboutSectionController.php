<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminAboutSectionController extends Controller
{
    /**
     * Only Super Admin can access this controller.
     */
    public function index(): View
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        return view('admin.about.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'about_description' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $data = [
            'about_description' => $validated['about_description'] ?? $settings->about_description,
            'hero_title' => $validated['hero_title'] ?? $settings->hero_title,
            'hero_subtitle' => $validated['hero_subtitle'] ?? $settings->hero_subtitle,
        ];

        if ($request->hasFile('about_image')) {
            if ($settings->about_image) {
                Storage::disk('public')->delete($settings->about_image);
            }
            $data['about_image'] = $request->file('about_image')->store('about', 'public');
        }

        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) {
                Storage::disk('public')->delete($settings->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('hero', 'public');
        }

        $settings->update($data);

        return redirect()->route('admin.about.index')->with('success', 'About section updated successfully.');
    }
}