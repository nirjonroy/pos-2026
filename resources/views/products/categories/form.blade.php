@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'name'])
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug ?? '') }}" placeholder="Auto-generated if blank" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'slug'])
    </div>

    <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
        <select id="parent_id" name="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">None</option>
            @foreach ($categories as $parentCategory)
                <option value="{{ $parentCategory->id }}" @selected((string) old('parent_id', $category->parent_id ?? '') === (string) $parentCategory->id)>
                    {{ $parentCategory->name }}
                </option>
            @endforeach
        </select>
        @include('products.partials.field-error', ['name' => 'parent_id'])
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="1" @selected((string) old('status', $category->status ?? '1') === '1')>Active</option>
            <option value="0" @selected((string) old('status', $category->status ?? '1') === '0')>Inactive</option>
        </select>
        @include('products.partials.field-error', ['name' => 'status'])
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
    <a href="{{ route('categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
</div>
