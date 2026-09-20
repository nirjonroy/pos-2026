@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $customer->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'name'])
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'phone'])
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $customer->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'email'])
    </div>

    <div>
        <label for="credit_limit" class="block text-sm font-medium text-gray-700">Credit Limit</label>
        <input id="credit_limit" name="credit_limit" type="number" step="0.01" min="0" value="{{ old('credit_limit', $customer->credit_limit ?? '0.00') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'credit_limit'])
    </div>

    <div>
        <label for="opening_due" class="block text-sm font-medium text-gray-700">Opening Due</label>
        <input id="opening_due" name="opening_due" type="number" step="0.01" min="0" value="{{ old('opening_due', $customer->opening_due ?? '0.00') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'opening_due'])
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="1" @selected((string) old('status', $customer->status ?? '1') === '1')>Active</option>
            <option value="0" @selected((string) old('status', $customer->status ?? '1') === '0')>Inactive</option>
        </select>
        @include('products.partials.field-error', ['name' => 'status'])
    </div>

    <div class="md:col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
        <textarea id="address" name="address" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $customer->address ?? '') }}</textarea>
        @include('products.partials.field-error', ['name' => 'address'])
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
    <a href="{{ route('customers.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
</div>
