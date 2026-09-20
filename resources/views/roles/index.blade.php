@extends('layouts.admin')

@section('page-title', 'Roles')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h2 class="font-semibold text-gray-900">Roles</h2>
            @if (auth()->user()->hasPermission('roles.manage'))
                <a href="{{ route('roles.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add Role</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Users</th>
                        <th class="px-5 py-3">Permissions</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($roles as $role)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $role->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $role->description ?: '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $role->users_count }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $role->permissions_count }}</td>
                            <td class="px-5 py-3 text-right">
                                @if (auth()->user()->hasPermission('roles.manage'))
                                    <a href="{{ route('roles.edit', $role) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $roles->links() }}</div>
    </div>
@endsection
