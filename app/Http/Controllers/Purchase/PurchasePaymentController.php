<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchasePaymentRequest;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class PurchasePaymentController extends Controller
{
    public function store(StorePurchasePaymentRequest $request, Purchase $purchase, PurchaseService $service): RedirectResponse
    {
        try {
            $service->addPayment($purchase, $request->validated(), $request->user()->id);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Payment added successfully.');
    }
}
