<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreExpenseCategoryRequest;
use App\Http\Requests\Accounting\UpdateExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $categories = ExpenseCategory::when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('accounting.expense-categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('accounting.expense-categories.create');
    }

    public function store(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        ExpenseCategory::create($request->validated());

        return redirect()->route('expense-categories.index')->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory): View
    {
        return view('accounting.expense-categories.edit', compact('expenseCategory'));
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->update($request->validated());

        return redirect()->route('expense-categories.index')->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->expenses()->exists()) {
            return back()->with('error', 'Category cannot be deleted because it is in use.');
        }

        $expenseCategory->delete();

        return back()->with('success', 'Expense category deleted successfully.');
    }
}
