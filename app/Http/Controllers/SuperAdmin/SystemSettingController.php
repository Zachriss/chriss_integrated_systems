<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        return view('super-admin.settings.index', compact('settings'));
    }

    public function appName()
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        return view('super-admin.settings.app-name', compact('settings'));
    }

    public function logo()
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        return view('super-admin.settings.logo', compact('settings'));
    }

    public function theme()
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        return view('super-admin.settings.theme', compact('settings'));
    }

    public function preferences()
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        return view('super-admin.settings.preferences', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'system_short_name' => 'nullable|string|max:50',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:255',
            'email_from_name' => 'nullable|string|max:255',
            'email_from_address' => 'nullable|email|max:255',
            'maintenance_mode' => 'nullable|boolean',
            'system_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:2048',
            'system_favicon' => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:1024',
            'login_background' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            // Social media fields
            'facebook_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:30',
            // About section
            'about_description' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // Contact section
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'contact_address' => 'nullable|string|max:500',
            'map_embed_url' => 'nullable|string|max:1000',
            // Hero section
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // System description
            'system_description' => 'nullable|string|max:1000',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $data = [
            'system_name' => $validated['system_name'],
            'system_short_name' => $validated['system_short_name'] ?? null,
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'],
            'accent_color' => $validated['accent_color'] ?? '#0d6efd',
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'footer_text' => $validated['footer_text'] ?? null,
            'email_from_name' => $validated['email_from_name'] ?? null,
            'email_from_address' => $validated['email_from_address'] ?? null,
            'maintenance_mode' => $request->boolean('maintenance_mode'),
            // Social media
            'facebook_url' => $validated['facebook_url'] ?? null,
            'twitter_url' => $validated['twitter_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            // About section
            'about_description' => $validated['about_description'] ?? null,
            // Contact section
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_address' => $validated['contact_address'] ?? null,
            'map_embed_url' => $validated['map_embed_url'] ?? null,
            // Hero section
            'hero_title' => $validated['hero_title'] ?? null,
            'hero_subtitle' => $validated['hero_subtitle'] ?? null,
            // System description
            'system_description' => $validated['system_description'] ?? null,
        ];

        if ($request->hasFile('system_logo')) {
            if ($settings->system_logo) {
                Storage::disk('public')->delete($settings->system_logo);
            }
            $data['system_logo'] = $request->file('system_logo')->store('settings/logos', 'public');
        }

        if ($request->hasFile('system_favicon')) {
            if ($settings->system_favicon) {
                Storage::disk('public')->delete($settings->system_favicon);
            }
            $data['system_favicon'] = $request->file('system_favicon')->store('settings/favicons', 'public');
        }

        if ($request->hasFile('login_background')) {
            if ($settings->login_background) {
                Storage::disk('public')->delete($settings->login_background);
            }
            $data['login_background'] = $request->file('login_background')->store('settings/backgrounds', 'public');
        }

        if ($request->hasFile('about_image')) {
            if ($settings->about_image) {
                Storage::disk('public')->delete($settings->about_image);
            }
            $data['about_image'] = $request->file('about_image')->store('settings/about', 'public');
        }

        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) {
                Storage::disk('public')->delete($settings->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('settings/hero', 'public');
        }

        $settings->update($data);

        // Cache is cleared automatically in the SystemSetting model

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings',
            'description' => 'Updated system settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'System settings updated successfully.');
    }

    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'system_short_name' => 'nullable|string|max:50',
            'system_description' => 'nullable|string|max:1000',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'email_from_name' => 'nullable|string|max:255',
            'email_from_address' => 'nullable|email|max:255',
            'maintenance_mode' => 'nullable|boolean',
            'system_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:2048',
            'system_favicon' => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:1024',
            'login_background' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $data = [
            'system_name' => $validated['system_name'],
            'system_short_name' => $validated['system_short_name'] ?? null,
            'system_description' => $validated['system_description'] ?? null,
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'],
            'accent_color' => $validated['accent_color'] ?? '#0d6efd',
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'email_from_name' => $validated['email_from_name'] ?? null,
            'email_from_address' => $validated['email_from_address'] ?? null,
            'maintenance_mode' => $request->boolean('maintenance_mode'),
        ];

        if ($request->hasFile('system_logo')) {
            if ($settings->system_logo) {
                Storage::disk('public')->delete($settings->system_logo);
            }
            $data['system_logo'] = $request->file('system_logo')->store('settings/logos', 'public');
        }

        if ($request->hasFile('system_favicon')) {
            if ($settings->system_favicon) {
                Storage::disk('public')->delete($settings->system_favicon);
            }
            $data['system_favicon'] = $request->file('system_favicon')->store('settings/favicons', 'public');
        }

        if ($request->hasFile('login_background')) {
            if ($settings->login_background) {
                Storage::disk('public')->delete($settings->login_background);
            }
            $data['login_background'] = $request->file('login_background')->store('settings/backgrounds', 'public');
        }

        $settings->update($data);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings - Branding',
            'description' => 'Updated branding & identity settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Branding settings updated successfully.');
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'contact_address' => 'nullable|string|max:500',
            'map_embed_url' => 'nullable|string|max:1000',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $settings->update([
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_address' => $validated['contact_address'] ?? null,
            'map_embed_url' => $validated['map_embed_url'] ?? null,
        ]);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings - Contact',
            'description' => 'Updated contact information settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Contact settings updated successfully.');
    }

    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:30',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $settings->update([
            'facebook_url' => $validated['facebook_url'] ?? null,
            'twitter_url' => $validated['twitter_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
        ]);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings - Social Media',
            'description' => 'Updated social media links',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Social media links updated successfully.');
    }

    public function updateHero(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $data = [
            'hero_title' => $validated['hero_title'] ?? null,
            'hero_subtitle' => $validated['hero_subtitle'] ?? null,
        ];

        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) {
                Storage::disk('public')->delete($settings->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('settings/hero', 'public');
        }

        $settings->update($data);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings - Hero',
            'description' => 'Updated hero section settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Hero section updated successfully.');
    }

    public function updateAbout(Request $request)
    {
        $validated = $request->validate([
            'about_description' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $data = [
            'about_description' => $validated['about_description'] ?? null,
        ];

        if ($request->hasFile('about_image')) {
            if ($settings->about_image) {
                Storage::disk('public')->delete($settings->about_image);
            }
            $data['about_image'] = $request->file('about_image')->store('settings/about', 'public');
        }

        $settings->update($data);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings - About',
            'description' => 'Updated about section settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'About section updated successfully.');
    }

    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'footer_text' => 'nullable|string|max:255',
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $settings->update([
            'footer_text' => $validated['footer_text'] ?? null,
        ]);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'System Settings - Footer',
            'description' => 'Updated footer settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Footer settings updated successfully.');
    }
}
