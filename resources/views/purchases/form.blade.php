@csrf

@php
    $oldItems = old('items');
    $rows = $oldItems ?: (($purchase ?? null)
        ? $purchase->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_cost' => $item->unit_cost,
            'discount' => $item->discount,
            'tax' => $item->tax,
        ])->values()->toArray()
        : [['product_id' => '', 'quantity' => '', 'unit_cost' => '', 'discount' => '0', 'tax' => '0']]);
@endphp

<div x-data="{ rows: {{ json_encode($rows) }} }" class="space-y-6">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Supplier</label>
            <select name="supplier_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $purchase->supplier_id ?? '') === (string) $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @include('products.partials.field-error', ['name' => 'supplier_id'])
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Branch</label>
            <select name="branch_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((string) old('branch_id', $purchase->branch_id ?? '') === (string) $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
            @include('products.partials.field-error', ['name' => 'branch_id'])
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Warehouse</label>
            <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">No warehouse</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id', $purchase->warehouse_id ?? '') === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
            @include('products.partials.field-error', ['name' => 'warehouse_id'])
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
            <input name="purchase_date" type="date" value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : now()->toDateString()) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @include('products.partials.field-error', ['name' => 'purchase_date'])
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Note</label>
            <textarea name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('note', $purchase->note ?? '') }}</textarea>
        </div>
    </div>

    <div>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Products</h2>
            <button type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="rows.push({ product_id: '', quantity: '', unit_cost: '', discount: '0', tax: '0' })">Add Row</button>
        </div>

        <div class="space-y-3">
            <template x-for="(row, index) in rows" :key="index">
                <div class="grid gap-3 rounded-md border border-gray-200 p-3 lg:grid-cols-6">
                    <select x-model="row.product_id" :name="`items[${index}][product_id]`" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 lg:col-span-2">
                        <option value="">Select product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                    <input x-model="row.quantity" :name="`items[${index}][quantity]`" type="number" step="0.001" min="0.001" placeholder="Qty" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <input x-model="row.unit_cost" :name="`items[${index}][unit_cost]`" type="number" step="0.01" min="0" placeholder="Unit cost" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <input x-model="row.discount" :name="`items[${index}][discount]`" type="number" step="0.01" min="0" placeholder="Discount" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <div class="flex gap-2">
                        <input x-model="row.tax" :name="`items[${index}][tax]`" type="number" step="0.01" min="0" placeholder="Tax" class="min-w-0 flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50" @click="rows.splice(index, 1)" x-show="rows.length > 1">Remove</button>
                    </div>
                </div>
            </template>
        </div>
        @include('products.partials.field-error', ['name' => 'items'])
    </div>

    <div class="flex items-center gap-3">
        <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Draft</button>
        <a href="{{ route('purchases.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
    </div>
</div>
