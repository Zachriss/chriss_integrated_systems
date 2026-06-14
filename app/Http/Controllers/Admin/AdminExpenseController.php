<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminExpenseController extends Controller
{
    const CATEGORIES = [
        'electricity' => 'Electricity',
        'rent' => 'Rent',
        'salaries' => 'Salaries',
        'internet' => 'Internet',
        'transport' => 'Transport',
        'maintenance' => 'Maintenance',
        'utilities' => 'Utilities',
        'other' => 'Other',
    ];

    public function index(Request $request)
    {
        $query = Expense::with('creator')
            ->orderByDesc('expense_date')
            ->orderByDesc('id');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->paginate(20);
        $categories = self::CATEGORIES;

        return view('admin.expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|in:electricity,rent,salaries,internet,transport,maintenance,utilities,other',
            'description' => 'nullable|string|max:1000',
            'expense_date' => 'required|date',
        ]);

        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'expense_date' => $validated['expense_date'],
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'role' => $request->user()->role,
            'action_type' => 'expense',
            'reference_id' => $expense->id,
            'description' => "Expense recorded: {$expense->title} - TZS {$expense->amount}",
            'date' => today(),
        ]);

        // AJAX response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense saved successfully.',
                'expense' => $expense->load('creator'),
            ]);
        }

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense saved successfully.');
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|in:electricity,rent,salaries,internet,transport,maintenance,utilities,other',
            'description' => 'nullable|string|max:1000',
            'expense_date' => 'required|date',
        ]);

        $expense->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully.',
                'expense' => $expense->load('creator'),
            ]);
        }

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $expense->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully.',
            ]);
        }

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}