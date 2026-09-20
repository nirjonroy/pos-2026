<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockTransferRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StockTransferController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'fromWarehouse', 'toWarehouse', 'creator'])
            ->when($search, fn ($query, string $search) => $query->where('transfer_no', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('inventory.transfers.index', compact('transfers', 'search'));
    }

    public function create(): View
    {
        return view('inventory.transfers.create', $this->formData());
    }

    public function store(StoreStockTransferRequest $request, StockTransferService $service): RedirectResponse
    {
        try {
            $transfer = $service->createCompleted($request->validated(), $request->user()->id);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'Stock transfer completed successfully.');
    }

    public function show(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load(['fromBranch', 'toBranch', 'fromWarehouse', 'toWarehouse', 'creator', 'items.product']);

        return view('inventory.transfers.show', compact('stockTransfer'));
    }

    private function formData(): array
    {
        return [
            'branches' => Branch::where('status', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('status', true)->orderBy('name')->get(),
            'products' => Product::where('status', true)->orderBy('name')->get(),
        ];
    }
}
