<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAssignment;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(): View
    {
        $admin = auth()->user();

        $products = Product::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id)
                ->where('can_manage_inventory', true);
        })->paginate(20);

        return view('admin.inventory.index', compact('products'));
    }

    public function show(Product $product): View
    {
        $admin = auth()->user();

        $isAssigned = AdminAssignment::where('admin_id', $admin->id)
            ->where('product_id', $product->id)
            ->where('can_manage_inventory', true)
            ->exists();

        abort_unless($isAssigned, 403);

        $sales = $product->saleItems()
            ->with('sale.customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.show', compact('product', 'sales'));
    }

    public function stockIn(Request $request, Product $product): RedirectResponse
    {
        $admin = auth()->user();

        $isAssigned = AdminAssignment::where('admin_id', $admin->id)
            ->where('product_id', $product->id)
            ->where('can_manage_inventory', true)
            ->exists();

        abort_unless($isAssigned, 403);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product->increment('quantity', $request->quantity);

        return redirect()->back()->with('success', "Added {$request->quantity} units to stock.");
    }

    public function stockOut(Request $request, Product $product): RedirectResponse
    {
        $admin = auth()->user();

        $isAssigned = AdminAssignment::where('admin_id', $admin->id)
            ->where('product_id', $product->id)
            ->where('can_manage_inventory', true)
            ->exists();

        abort_unless($isAssigned, 403);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
            'notes' => 'nullable|string',
        ]);

        $product->decrement('quantity', $request->quantity);

        return redirect()->back()->with('success', "Removed {$request->quantity} units from stock.");
    }

    public function lowStock(): View
    {
        $admin = auth()->user();

        $products = Product::whereHas('adminAssignments', function ($query) use ($admin) {
            $query->where('admin_id', $admin->id)
                ->where('can_manage_inventory', true);
        })->get()->filter(function ($product) {
            return $product->isLowStock();
        });

        return view('admin.inventory.low-stock', compact('products'));
    }
}