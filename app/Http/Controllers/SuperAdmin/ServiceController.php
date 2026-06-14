<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category', 'createdBy')->latest()->get();
        $categories = ServiceCategory::where('is_active', true)->get();
        return view('super-admin.services.index', compact('services', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:service_categories,id',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'in:active,inactive',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $validated;
        $data['slug'] = Str::slug($validated['name']);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('services/images', 'public');
        }

        $gallery = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $gallery[] = $image->store('services/gallery', 'public');
            }
        }
        $data['gallery_images'] = $gallery;

        // Map price to base_price
        if (isset($data['price'])) {
            $data['base_price'] = $data['price'];
            unset($data['price']);
        }

        $service = Service::create($data);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'create',
            'module' => 'Services',
            'description' => "Created service: {$service->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'service' => $service->load('category', 'createdBy')
        ]);
    }

    public function show(Service $service)
    {
        return response()->json([
            'success' => true,
            'service' => $service->load('category', 'createdBy')
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:service_categories,id',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'in:active,inactive',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $validated;
        $data['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('featured_image')) {
            if ($service->featured_image) {
                Storage::disk('public')->delete($service->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('services/images', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $gallery = $service->gallery_images ?? [];
            foreach ($request->file('gallery_images') as $image) {
                $gallery[] = $image->store('services/gallery', 'public');
            }
            $data['gallery_images'] = $gallery;
        }

        // Map price to base_price
        if (isset($data['price'])) {
            $data['base_price'] = $data['price'];
            unset($data['price']);
        }

        $service->update($data);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'Services',
            'description' => "Updated service: {$service->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'service' => $service->load('category', 'createdBy')
        ]);
    }

    public function destroy(Request $request, Service $service)
    {
        if ($service->featured_image) {
            Storage::disk('public')->delete($service->featured_image);
        }

        if ($service->gallery_images) {
            foreach ($service->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'delete',
            'module' => 'Services',
            'description' => "Deleted service: {$service->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.'
        ]);
    }

    public function toggleStatus(Request $request, Service $service)
    {
        $service->update(['status' => $service->status === 'active' ? 'inactive' : 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Service status updated.',
            'status' => $service->status
        ]);
    }
}