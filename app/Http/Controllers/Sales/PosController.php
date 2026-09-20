<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSaleRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\SaleService;
use RuntimeException;

class PosController extends Controller
{
    public function index(): View
    {
        return view('pos.index', [
            'branches' => Branch::where('status', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('status', true)->orderBy('name')->get(),
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'categories' => Category::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $categoryId = $request->input('category_id');
        $branchId = $request->input('branch_id');
        $warehouseId = $request->input('warehouse_id');

        $stockSubquery = DB::table('inventories')
            ->selectRaw('product_id, SUM(quantity - reserved_quantity) as available_stock')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->when($warehouseId !== null && $warehouseId !== '', fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->groupBy('product_id');

        $products = Product::query()
            ->select([
                'products.id',
                'products.name',
                'products.sku',
                'products.barcode',
                'products.selling_price',
                DB::raw('COALESCE(stock.available_stock, 0) as available_stock'),
            ])
            ->leftJoinSub($stockSubquery, 'stock', fn ($join) => $join->on('stock.product_id', '=', 'products.id'))
            ->where('products.status', true)
            ->when($categoryId, fn ($query) => $query->where('products.category_id', $categoryId))
            ->when($search, function ($query, string $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('products.name', 'like', "%{$search}%")
                        ->orWhere('products.sku', 'like', "%{$search}%")
                        ->orWhere('products.barcode', 'like', "%{$search}%");
                });
            })
            ->orderBy('products.name')
            ->limit(60)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'selling_price' => (float) $product->selling_price,
                'available_stock' => (float) $product->available_stock,
            ]);

        return response()->json($products);
    }

    public function checkout(StoreSaleRequest $request, SaleService $saleService): JsonResponse
    {
        try {
            $result = $saleService->checkout($request->validated(), $request->user()->id);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $result['receipt_url'] = route('sales.receipt', $result['sale_id']);

        return response()->json($result);
    }
}
