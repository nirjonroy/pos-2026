@extends('layouts.admin')

@section('page-title', 'Create Stock Adjustment')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('stock-adjustments.store') }}" x-data="{ rows: {{ json_encode(old('items', [['product_id' => '', 'quantity' => '', 'unit_cost' => '0']])) }} }" onsubmit="return confirm('Complete this stock adjustment?')">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Branch</label>
                    <select name="branch_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @include('products.partials.field-error', ['name' => 'branch_id'])
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Warehouse</label>
                    <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">No warehouse</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    @include('products.partials.field-error', ['name' => 'warehouse_id'])
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="increase" @selected(old('type') === 'increase')>Increase</option>
                        <option value="decrease" @selected(old('type') === 'decrease')>Decrease</option>
                    </select>
                    @include('products.partials.field-error', ['name' => 'type'])
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Reason</label>
                    <input name="reason" value="{{ old('reason') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @include('products.partials.field-error', ['name' => 'reason'])
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Note</label>
                    <textarea name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="mt-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Products</h2>
                    <button type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="rows.push({ product_id: '', quantity: '', unit_cost: '0' })">Add Row</button>
                </div>

                <div class="space-y-3">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="grid gap-3 rounded-md border border-gray-200 p-3 md:grid-cols-4">
                            <select x-model="row.product_id" :name="`items[${index}][product_id]`" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                            <input x-model="row.quantity" :name="`items[${index}][quantity]`" type="number" step="0.001" min="0.001" placeholder="Quantity" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <input x-model="row.unit_cost" :name="`items[${index}][unit_cost]`" type="number" step="0.01" min="0" placeholder="Unit cost" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50" @click="rows.splice(index, 1)" x-show="rows.length > 1">Remove</button>
                        </div>
                    </template>
                </div>
                @include('products.partials.field-error', ['name' => 'items'])
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Complete Adjustment</button>
                <a href="{{ route('stock-adjustments.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
