@extends('layouts.admin')

@section('page-title', 'Inventory Stock')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid gap-3 border-b border-gray-200 px-5 py-4 md:grid-cols-5">
            <input name="search" value="{{ $search }}" placeholder="Product, SKU, barcode" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <select name="branch_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All branches</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((string) $branchId === (string) $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
            <select name="warehouse_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All warehouses</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((string) $warehouseId === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
            <select name="stock_status" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All statuses</option>
                <option value="out" @selected($stockStatus === 'out')>Out of Stock</option>
                <option value="low" @selected($stockStatus === 'low')>Low Stock</option>
                <option value="in" @selected($stockStatus === 'in')>In Stock</option>
            </select>
            <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">SKU</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3">Warehouse</th>
                        <th class="px-5 py-3">Current Quantity</th>
                        <th class="px-5 py-3">Reserved Quantity</th>
                        <th class="px-5 py-3">Available Quantity</th>
                        <th class="px-5 py-3">Minimum Stock</th>
                        <th class="px-5 py-3">Stock Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($inventories as $inventory)
                        @php
                            $quantity = (float) $inventory->quantity;
                            $reserved = (float) $inventory->reserved_quantity;
                            $available = $quantity - $reserved;
                            $minimum = (float) $inventory->product->minimum_stock;
                            $status = $quantity <= 0 ? 'Out of Stock' : ($quantity <= $minimum ? 'Low Stock' : 'In Stock');
                            $statusClass = $status === 'Out of Stock' ? 'bg-red-100 text-red-700' : ($status === 'Low Stock' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
                        @endphp
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $inventory->product->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $inventory->product->sku }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $inventory->branch->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $inventory->warehouse?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format($quantity, 3) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format($reserved, 3) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format($available, 3) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format($minimum, 3) }}</td>
                            <td class="px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-8 text-center text-gray-500">No inventory found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $inventories->links() }}</div>
    </div>
@endsection
