<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::active()
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'in-stock') {
                $query->where('quantity', '>', 0);
            } elseif ($request->stock === 'out-of-stock') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->stock === 'low-stock') {
                $query->where('quantity', '>', 0)
                      ->whereColumn('quantity', '<=', 'low_stock_alert_level');
            }
        }

        if ($request->filled('featured') && $request->featured === '1') {
            $query->featured();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with(['category', 'galleries'])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }

    /**
     * AJAX search endpoint for the shop
     */
    public function search(Request $request): JsonResponse
    {
        $query = Product::active()->with('category');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->take(20)->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'selling_price' => $product->selling_price,
                'image_url' => $product->image_url,
                'quantity' => $product->quantity,
                'category_name' => $product->category?->name,
                'stock_status' => $product->quantity <= 0 ? 'out' : ($product->isLowStock() ? 'low' : 'in'),
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
            'total' => count($products),
        ]);
    }

    /**
     * Get featured products for homepage AJAX
     */
    public function featured(): JsonResponse
    {
        $products = Product::active()
            ->featured()
            ->with('category')
            ->inStock()
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'selling_price' => $product->selling_price,
                    'image_url' => $product->image_url,
                    'quantity' => $product->quantity,
                    'category_name' => $product->category?->name,
                    'is_featured' => true,
                ];
            });

        return response()->json([
            'success' => true,
            'products' => $products,
            'total' => count($products),
        ]);
    }
}