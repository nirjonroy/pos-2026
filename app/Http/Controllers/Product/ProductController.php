<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $products = Product::with(['category', 'brand', 'unit'])
            ->when($search, function ($query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        return view('products.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', array_merge($this->formData(), compact('product')));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($this->hasRelatedRecords($product)) {
            return back()->with('error', 'Product cannot be deleted because it is in use.');
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::where('status', true)->orderBy('name')->get(),
            'brands' => Brand::where('status', true)->orderBy('name')->get(),
            'units' => Unit::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function hasRelatedRecords(Product $product): bool
    {
        $tables = [
            'inventories',
            'stock_movements',
            'stock_adjustment_items',
            'stock_transfer_items',
            'sale_items',
            'sale_return_items',
        ];

        foreach ($tables as $table) {
            if (DB::table($table)->where('product_id', $product->id)->exists()) {
                return true;
            }
        }

        return false;
    }
}
