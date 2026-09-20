@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'name'])
    </div>

    <div>
        <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'sku'])
    </div>

    <div>
        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
        <input id="barcode" name="barcode" type="text" value="{{ old('barcode', $product->barcode ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'barcode'])
    </div>

    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
        <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @include('products.partials.field-error', ['name' => 'category_id'])
    </div>

    <div>
        <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand</label>
        <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">No brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected((string) old('brand_id', $product->brand_id ?? '') === (string) $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        @include('products.partials.field-error', ['name' => 'brand_id'])
    </div>

    <div>
        <label for="unit_id" class="block text-sm font-medium text-gray-700">Unit</label>
        <select id="unit_id" name="unit_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Select unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected((string) old('unit_id', $product->unit_id ?? '') === (string) $unit->id)>{{ $unit->name }} ({{ $unit->short_name }})</option>
            @endforeach
        </select>
        @include('products.partials.field-error', ['name' => 'unit_id'])
    </div>

    <div>
        <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price</label>
        <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price ?? '0.00') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'cost_price'])
    </div>

    <div>
        <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price</label>
        <input id="selling_price" name="selling_price" type="number" step="0.01" min="0" value="{{ old('selling_price', $product->selling_price ?? '0.00') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'selling_price'])
    </div>

    <div>
        <label for="minimum_stock" class="block text-sm font-medium text-gray-700">Minimum Stock</label>
        <input id="minimum_stock" name="minimum_stock" type="number" step="0.001" min="0" value="{{ old('minimum_stock', $product->minimum_stock ?? '0.000') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'minimum_stock'])
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="1" @selected((string) old('status', $product->status ?? '1') === '1')>Active</option>
            <option value="0" @selected((string) old('status', $product->status ?? '1') === '0')>Inactive</option>
        </select>
        @include('products.partials.field-error', ['name' => 'status'])
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $product->description ?? '') }}</textarea>
        @include('products.partials.field-error', ['name' => 'description'])
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
    <a href="{{ route('products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
</div>
