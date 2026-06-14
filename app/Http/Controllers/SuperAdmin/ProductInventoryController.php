<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductInventoryController extends Controller
{
    public function index(): View
    {
        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('super-admin.inventory.products', compact('categories'));
    }

    public function dataTable(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'creator']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === 'true');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'in-stock':
                    $query->where('quantity', '>', 0);
                    break;
                case 'low-stock':
                    $query->whereColumn('quantity', '>', 0)
                          ->whereRaw('quantity <= low_stock_alert_level');
                    break;
                case 'out-of-stock':
                    $query->where('quantity', '<=', 0);
                    break;
            }
        }

        $products = $query->latest()->paginate(20);

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_alert_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'barcode' => 'nullable|string|max:255',
            'status' => 'in:active,inactive',
            'is_featured' => 'boolean',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['image', 'gallery_images']);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product = Product::create($data);

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                $path = $image->store('products/gallery', 'public');
                $product->galleries()->create([
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        if ($request->quantity > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'in',
                'quantity' => $request->quantity,
                'balance_before' => 0,
                'balance_after' => $request->quantity,
                'reference' => 'Initial Stock',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'product' => $product->load(['category', 'creator']),
        ]);
    }

    public function stats(): JsonResponse
    {
        $total = Product::count();
        $totalActive = Product::where('status', 'active')->count();
        $lowStock = Product::where('quantity', '>', 0)->whereColumn('quantity', '<=', 'low_stock_alert_level')->count();
        $outOfStock = Product::where('quantity', '<=', 0)->count();
        $featured = Product::where('is_featured', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'total_active' => $totalActive,
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
                'featured' => $featured,
            ]
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product' => $product->load(['category', 'creator', 'galleries']),
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:product_categories,id',
            'brand' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_alert_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'barcode' => 'nullable|string|max:255',
            'status' => 'in:active,inactive',
            'is_featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['image']);
        $oldQuantity = $product->quantity;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product->update($data);

        if ($request->quantity != $oldQuantity) {
            $difference = $request->quantity - $oldQuantity;
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $difference > 0 ? 'in' : 'out',
                'quantity' => abs($difference),
                'balance_before' => $oldQuantity,
                'balance_after' => $request->quantity,
                'reference' => 'Stock Adjustment',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product->load(['category', 'creator']),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->galleries as $gallery) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $product->galleries()->delete();
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully.',
            'status' => $product->status,
        ]);
    }

    public function restock(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldQuantity = $product->quantity;
        $product->increment('quantity', $request->quantity);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => $request->quantity,
            'balance_before' => $oldQuantity,
            'balance_after' => $product->fresh()->quantity,
            'reference' => 'Restock',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Added {$request->quantity} units to stock.",
            'product' => $product->fresh(),
        ]);
    }
}