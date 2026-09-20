@csrf
<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input name="name" value="{{ old('name', $expenseCategory->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'name'])
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="1" @selected((string) old('status', $expenseCategory->status ?? '1') === '1')>Active</option>
            <option value="0" @selected((string) old('status', $expenseCategory->status ?? '1') === '0')>Inactive</option>
        </select>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
    <a href="{{ route('expense-categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
</div>
