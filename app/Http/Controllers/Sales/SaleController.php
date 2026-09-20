<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->input('status');
        $branchId = $request->input('branch_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $sales = Sale::with(['customer', 'branch', 'creator'])
            ->when($search, function ($query, string $search) {
                $query->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$search}%"));
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->when($dateFrom, fn ($query) => $query->whereDate('sale_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('sale_date', '<=', $dateTo))
            ->latest('sale_date')
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        return view('sales.index', compact('sales', 'branches', 'search', 'status', 'branchId', 'dateFrom', 'dateTo'));
    }

    public function show(Sale $sale): View
    {
        $sale->load([
            'customer',
            'branch',
            'warehouse',
            'creator',
            'items.product',
            'payments.receiver',
            'returns.items.product',
        ]);

        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale): View
    {
        $sale->load(['branch', 'warehouse', 'customer', 'creator', 'items.product', 'payments.receiver']);

        return view('sales.receipt', compact('sale'));
    }
}
