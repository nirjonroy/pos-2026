<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSaleReturnRequest;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\SaleReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ReturnController extends Controller
{
    public function create(Sale $sale, SaleReturnService $service): View|RedirectResponse
    {
        if ($sale->status === 'cancelled') {
            return redirect()->route('sales.show', $sale)->with('error', 'Cancelled sales cannot be returned.');
        }

        $sale->load(['customer', 'branch', 'warehouse', 'items.product']);
        $returnableItems = $service->returnableItems($sale);

        return view('sales.returns.create', compact('sale', 'returnableItems'));
    }

    public function store(StoreSaleReturnRequest $request, Sale $sale, SaleReturnService $service): RedirectResponse
    {
        try {
            $saleReturn = $service->createReturn($sale, $request->validated(), $request->user()->id);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('sale-returns.show', $saleReturn)->with('success', 'Sale return completed successfully.');
    }

    public function show(SaleReturn $saleReturn): View
    {
        $saleReturn->load(['sale.customer', 'sale.branch', 'items.product', 'refundPayments.creator', 'creator']);

        return view('sales.returns.show', compact('saleReturn'));
    }
}
