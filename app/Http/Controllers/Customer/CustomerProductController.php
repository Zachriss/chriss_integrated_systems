<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerProductAssignment;
use App\Models\Product;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class CustomerProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        $products = $customer ? $customer->productAssignments()
            ->with('product.category')
            ->orderByDesc('assigned_date')
            ->paginate(20) : collect();

        return view('customer.my-products', compact('products'));
    }

    public function browse(Request $request)
    {
        $query = Product::active()
            ->with('category')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(12);

        return view('customer.browse-products', compact('products'));
    }

    public function order(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found.'], 400);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:2000',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->quantity <= 0) {
            return response()->json(['success' => false, 'message' => 'This product is out of stock.'], 400);
        }

        if ($validated['quantity'] > $product->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Only {$product->quantity} units available. Please reduce quantity."
            ], 400);
        }

        // Create a service request for the product order
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $customer->id,
            'service_id' => $this->getOrCreateProductOrderService(),
            'status' => 'pending',
            'notes' => "Product Order:\nProduct: {$product->name}\nQuantity: {$validated['quantity']}\nPrice per unit: TZS " . number_format($product->selling_price, 0) . "\n\nNotes: " . ($validated['notes'] ?? 'N/A'),
            'cost' => $product->selling_price * $validated['quantity'],
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'product_order',
            'reference_id' => $serviceRequest->id,
            'description' => "Product order #{$serviceRequest->id}: {$product->name} x{$validated['quantity']}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Your order for {$product->name} x{$validated['quantity']} has been placed. Staff will contact you shortly.",
            'request' => $serviceRequest->load('service'),
        ]);
    }

    /**
     * Get or create a "Product Order" service for generating service requests
     */
    private function getOrCreateProductOrderService()
    {
        $service = \App\Models\Service::where('slug', 'product-order')
            ->orWhere('name', 'Product Order')
            ->first();

        if (!$service) {
            $category = \App\Models\ServiceCategory::firstOrCreate(
                ['slug' => 'orders'],
                ['name' => 'Orders', 'is_active' => true]
            );

            $service = \App\Models\Service::create([
                'name' => 'Product Order',
                'slug' => 'product-order',
                'category_id' => $category->id,
                'short_description' => 'Customer product ordering',
                'status' => 'active',
                'base_price' => 0,
            ]);
        }

        return $service->id;
    }
}