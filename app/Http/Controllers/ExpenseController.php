<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $expenseQuery = Expense::query();

        if ($request->has('category') && $request->category != 0) {
            $expenseQuery->where('category_id', $request->category);
        }

        $expenses = $expenseQuery->latest()->get();
        $total = $expenses->sum('amount');

        // Group expenses by category and calculate the total for each category
        $categoryTotals = $expenses->groupBy('category_id')->map(function ($categoryExpenses) {
            return $categoryExpenses->sum('amount');
        });

        $nameTotals = $expenses->groupBy('name')->map(function ($nameExpense) {
            return $nameExpense->sum('amount');
        });

        return view('expenses.index', compact('expenses', 'total', 'categories', 'categoryTotals', 'nameTotals'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',  // Validate category
        ]);

        $category = $request->category;
        if ($request->category == 0) {
            $category = null;
        }

        Expense::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'category_id' => $category,  // Save category directly
        ]);

        return redirect()->route('expenses.index');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id); // Find the expense by ID
        $categories = Category::all(); // Get all categories for the select dropdown

        return view('expenses.edit', compact('expense', 'categories'))->with('success', 'Expense added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id', // Validate category
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }



    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect('/')->with('success', 'Expense deleted!');
    }

    

}

