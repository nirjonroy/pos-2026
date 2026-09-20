<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Requests\Purchase\UpdatePurchaseRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->input('status');

        $purchases = Purchase::with(['supplier', 'branch', 'warehouse', 'creator'])
            ->when($search, function ($query, string $search) {
                $query->where('purchase_no', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', "%{$search}%"));
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'search', 'status'));
    }

    public function create(): View
    {
        return view('purchases.create', $this->formData());
    }

    public function store(StorePurchaseRequest $request, PurchaseService $service): RedirectResponse
    {
        $purchase = $service->createDraft($request->validated(), $request->user()->id);

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase draft created successfully.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'branch', 'warehouse', 'creator', 'items.product', 'payments.creator']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View|RedirectResponse
    {
        if ($purchase->status !== 'draft') {
            return redirect()->route('purchases.show', $purchase)->with('error', 'Only draft purchases can be edited.');
        }

        $purchase->load('items');

        return view('purchases.edit', array_merge($this->formData(), compact('purchase')));
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase, PurchaseService $service): RedirectResponse
    {
        try {
            $service->updateDraft($purchase, $request->validated());
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        if ($purchase->status === 'received') {
            return back()->with('error', 'Received purchases cannot be deleted.');
        }

        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }

    public function receive(Purchase $purchase, PurchaseService $service): RedirectResponse
    {
        try {
            $service->receive($purchase);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Purchase received successfully.');
    }

    public function cancel(Purchase $purchase, PurchaseService $service): RedirectResponse
    {
        try {
            $service->cancel($purchase);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Purchase cancelled successfully.');
    }

    private function formData(): array
    {
        return [
            'suppliers' => Supplier::where('status', true)->orderBy('name')->get(),
            'branches' => Branch::where('status', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('status', true)->orderBy('name')->get(),
            'products' => Product::where('status', true)->orderBy('name')->get(),
        ];
    }
}
