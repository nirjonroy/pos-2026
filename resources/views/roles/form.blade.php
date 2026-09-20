@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $role->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'name'])
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <input id="description" name="description" type="text" value="{{ old('description', $role->description ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @include('products.partials.field-error', ['name' => 'description'])
    </div>
</div>

<div class="mt-6 space-y-4">
    <h2 class="font-semibold text-gray-900">Permissions</h2>
    @include('products.partials.field-error', ['name' => 'permissions'])

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($permissions as $group => $groupPermissions)
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">{{ $group ?: 'General' }}</h3>
                <div class="space-y-2">
                    @foreach ($groupPermissions as $permission)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                @checked(in_array($permission->id, old('permissions', isset($role) ? $role->permissions->pluck('id')->all() : [])))>
                            <span>{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
    <a href="{{ route('roles.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
</div>
