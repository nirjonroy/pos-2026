<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $branchId = $request->input('branch_id');
        $warehouseId = $request->input('warehouse_id');
        $stockStatus = $request->input('stock_status');

        $inventories = Inventory::with(['product', 'branch', 'warehouse'])
            ->when($search, function ($query, string $search) {
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->when($warehouseId !== null && $warehouseId !== '', fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->when($stockStatus, function ($query, string $stockStatus) {
                if ($stockStatus === 'out') {
                    $query->where('quantity', '<=', 0);
                } elseif ($stockStatus === 'low') {
                    $query->where('quantity', '>', 0)
                        ->whereHas('product', fn ($productQuery) => $productQuery->whereColumn('inventories.quantity', '<=', 'products.minimum_stock'));
                } elseif ($stockStatus === 'in') {
                    $query->whereHas('product', fn ($productQuery) => $productQuery->whereColumn('inventories.quantity', '>', 'products.minimum_stock'));
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('inventory.index', compact('inventories', 'branches', 'warehouses', 'search', 'branchId', 'warehouseId', 'stockStatus'));
    }
}
