<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View
    {
        return view('super-admin.inventory.categories');
    }

    public function dataTable(): JsonResponse
    {
        $categories = ProductCategory::withCount('products')
            ->latest()
            ->paginate(20);

        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'is_active']);
        $data['slug'] = \Illuminate\Support\Str::slug($request->name);

        $category = ProductCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => $category,
        ]);
    }

    public function show(ProductCategory $category): JsonResponse
    {
        return response()->json([
            'category' => $category->loadCount('products'),
        ]);
    }

    public function update(Request $request, ProductCategory $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'is_active']);
        $data['slug'] = \Illuminate\Support\Str::slug($request->name);

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'category' => $category,
        ]);
    }

    public function destroy(ProductCategory $category): JsonResponse
    {
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with products.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    public function toggleStatus(ProductCategory $category): JsonResponse
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category status updated.',
            'is_active' => $category->is_active,
        ]);
    }
}