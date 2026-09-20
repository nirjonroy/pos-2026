<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockAdjustmentRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StockAdjustmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $adjustments = StockAdjustment::with(['branch', 'warehouse', 'creator'])
            ->when($search, function ($query, string $search) {
                $query->where('adjustment_no', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('inventory.adjustments.index', compact('adjustments', 'search'));
    }

    public function create(): View
    {
        return view('inventory.adjustments.create', $this->formData());
    }

    public function store(StoreStockAdjustmentRequest $request, StockAdjustmentService $service): RedirectResponse
    {
        try {
            $adjustment = $service->createCompleted($request->validated(), $request->user()->id);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('stock-adjustments.show', $adjustment)->with('success', 'Stock adjustment completed successfully.');
    }

    public function show(StockAdjustment $stockAdjustment): View
    {
        $stockAdjustment->load(['branch', 'warehouse', 'creator', 'items.product']);

        return view('inventory.adjustments.show', compact('stockAdjustment'));
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
