<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function receipt(Sale $sale): View
    {
        $sale->load(['branch', 'warehouse', 'customer', 'creator', 'items.product', 'payments.receiver']);

        return view('sales.receipt', compact('sale'));
    }
}
