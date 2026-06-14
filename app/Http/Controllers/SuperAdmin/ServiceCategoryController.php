<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with('createdBy')->withCount('services')->latest()->get();
        return view('super-admin.services.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['created_by'] = auth()->id();

        $category = ServiceCategory::create($validated);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'create',
            'module' => 'Service Categories',
            'description' => "Created service category: {$category->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => $category->loadCount('services'),
        ]);
    }

    public function show(ServiceCategory $serviceCategory)
    {
        return response()->json([
            'success' => true,
            'category' => $serviceCategory->loadCount('services'),
        ]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $serviceCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $serviceCategory->update($validated);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'Service Categories',
            'description' => "Updated service category: {$serviceCategory->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'category' => $serviceCategory->loadCount('services'),
        ]);
    }

    public function destroy(Request $request, ServiceCategory $serviceCategory)
    {
        $name = $serviceCategory->name;

        if ($serviceCategory->services()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing services. Reassign or delete the services first.',
            ], 422);
        }

        $serviceCategory->delete();

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'delete',
            'module' => 'Service Categories',
            'description' => "Deleted service category: {$name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    public function toggleStatus(Request $request, ServiceCategory $serviceCategory)
    {
        $serviceCategory->update(['is_active' => !$serviceCategory->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Category status updated.',
            'is_active' => $serviceCategory->is_active,
        ]);
    }
}