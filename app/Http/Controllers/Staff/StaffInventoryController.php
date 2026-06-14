<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Http\Request;

class StaffInventoryController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->paginate(20);

        return view('staff.inventory', compact('products'));
    }

    public function stockOut(Request $request, Product $product)
    {
        $user = $request->user();

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
        ]);

        $product->decrement('quantity', $validated['quantity']);

        ActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action_type' => 'inventory',
            'reference_id' => $product->id,
            'description' => "Stock-out: {$validated['quantity']} units of {$product->name}",
            'date' => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$validated['quantity']} unit(s) removed from {$product->name}.",
            'new_quantity' => $product->fresh()->quantity,
        ]);
    }
}