@extends('layouts.admin')

@section('page-title', 'Products')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
                <input name="search" value="{{ $search }}" placeholder="Search products, SKU, barcode" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-80">
                <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Search</button>
            </form>
            <a href="{{ route('products.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add Product</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">SKU</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Brand</th>
                        <th class="px-5 py-3">Cost</th>
                        <th class="px-5 py-3">Selling Price</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->barcode ?: 'No barcode' }}</div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $product->sku }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $product->brand?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $product->cost_price, 2) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $product->selling_price, 2) }}</td>
                            <td class="px-5 py-3">@include('products.partials.status', ['status' => $product->status])</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('products.edit', $product) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="ml-3 inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-medium text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-500">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $products->links() }}</div>
    </div>
@endsection
