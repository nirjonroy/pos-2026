@extends('layouts.admin')

@section('page-title', 'User Details')

@section('content')
    <div class="space-y-5">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Roles</p>
                    <p class="font-semibold text-gray-900">{{ $user->roles->pluck('name')->join(', ') ?: 'No role' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-semibold text-gray-900">Permissions</h2>
            <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($user->roles->flatMap->permissions->unique('id')->sortBy('group_name') as $permission)
                    <span class="rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-700">{{ $permission->slug }}</span>
                @empty
                    <p class="text-sm text-gray-500">No permissions assigned.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
