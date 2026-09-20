<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreExpenseRequest;
use App\Http\Requests\Accounting\UpdateExpenseRequest;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\RefundPayment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $categoryId = $request->input('expense_category_id');
        $branchId = $request->input('branch_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $expenses = Expense::with(['category', 'branch', 'creator'])
            ->when($search, fn ($query) => $query->where('expense_no', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"))
            ->when($categoryId, fn ($query) => $query->where('expense_category_id', $categoryId))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->when($dateFrom, fn ($query) => $query->whereDate('expense_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('expense_date', '<=', $dateTo))
            ->latest('expense_date')
            ->paginate(15)
            ->withQueryString();

        return view('accounting.expenses.index', [
            'expenses' => $expenses,
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'branches' => Branch::orderBy('name')->get(),
            'search' => $search,
            'categoryId' => $categoryId,
            'branchId' => $branchId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create(): View
    {
        return view('accounting.expenses.create', $this->formData());
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        Expense::create(array_merge($request->validated(), [
            'expense_no' => $this->nextExpenseNo(),
            'created_by' => $request->user()->id,
        ]));

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense): View
    {
        $expense->load(['category', 'branch', 'creator']);

        return view('accounting.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        return view('accounting.expenses.edit', array_merge($this->formData(), compact('expense')));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $expense->update($request->validated());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }

    public function summary(): View
    {
        $salesPayments = (float) Payment::sum('amount');
        $purchasePayments = (float) PurchasePayment::sum('amount');
        $expenses = (float) Expense::sum('amount');
        $refunds = (float) RefundPayment::sum('amount');

        return view('accounting.summary', [
            'totalSales' => (float) Sale::sum('total'),
            'totalPurchase' => (float) Purchase::sum('total'),
            'totalExpenses' => $expenses,
            'salesRefunds' => $refunds,
            'purchaseDue' => (float) Purchase::sum('due_amount'),
            'salesDue' => (float) Sale::sum('due_amount'),
            'netCashFlow' => $salesPayments - $purchasePayments - $expenses - $refunds,
        ]);
    }

    private function formData(): array
    {
        return [
            'categories' => ExpenseCategory::where('status', true)->orderBy('name')->get(),
            'branches' => Branch::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function nextExpenseNo(): string
    {
        $prefix = 'EXP-'.now()->format('Ymd').'-';
        $count = Expense::where('expense_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
