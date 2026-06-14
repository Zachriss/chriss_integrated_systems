<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $featuredProducts = Product::active()->featured()->with('category')->inStock()->take(8)->get();
        $featuredServices = Service::where('status', 'active')->where('is_featured', true)->with('category')->take(6)->get();
        $latestProducts = Product::active()->with('category')->latest()->take(8)->get();
        $productCategories = ProductCategory::active()->orderBy('name')->get();
        $serviceCategories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('marketplace.index', compact(
            'featuredProducts', 'featuredServices', 'latestProducts',
            'productCategories', 'serviceCategories'
        ));
    }

    public function products(Request $request): View
    {
        $query = Product::active()->with('category');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name','like',"%{$s}%")->orWhere('short_description','like',"%{$s}%");
            });
        }
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('sort')) {
            match($request->sort) {
                'price_asc' => $query->orderBy('selling_price'),
                'price_desc' => $query->orderByDesc('selling_price'),
                'newest' => $query->latest(),
                default => $query->orderByDesc('is_featured')->orderBy('name'),
            };
        } else {
            $query->orderByDesc('is_featured')->orderBy('name');
        }
        $products = $query->paginate(12)->withQueryString();
        $categories = ProductCategory::active()->orderBy('name')->get();
        return view('marketplace.products', compact('products', 'categories'));
    }

    public function services(Request $request): View
    {
        $query = Service::where('status', 'active')->with('category');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name','like',"%{$s}%")->orWhere('short_description','like',"%{$s}%");
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        $services = $query->orderByDesc('is_featured')->orderBy('name')->paginate(12)->withQueryString();
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        return view('marketplace.services', compact('services', 'categories'));
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q', '');

        $products = Product::active()->with('category')
            ->where(function($q) use ($term) {
                $q->where('name','like',"%{$term}%")->orWhere('short_description','like',"%{$term}%");
            })->take(5)->get()->map(fn($p) => [
                'type' => 'product', 'id' => $p->id, 'name' => $p->name,
                'slug' => $p->slug, 'price' => $p->selling_price,
                'image' => $p->image_url, 'url' => route('shop.show', $p->slug ?? $p->id),
                'stock' => $p->quantity,
            ]);

        $services = Service::where('status','active')->with('category')
            ->where(function($q) use ($term) {
                $q->where('name','like',"%{$term}%")->orWhere('short_description','like',"%{$term}%");
            })->take(5)->get()->map(fn($s) => [
                'type' => 'service', 'id' => $s->id, 'name' => $s->name,
                'slug' => $s->slug, 'price' => $s->base_price,
                'image' => $s->featured_image_url,
                'url' => route('services.show', $s->slug),
            ]);

        return response()->json([
            'success' => true,
            'results' => $products->concat($services)->take(8),
            'total' => $products->count() + $services->count(),
        ]);
    }
}