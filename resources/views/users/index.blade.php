@extends('layouts.admin')

@section('page-title', 'Users')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
                <input name="search" value="{{ $search }}" placeholder="Search users" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-72">
                <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Search</button>
            </form>
            @if (auth()->user()->hasPermission('users.manage'))
                <a href="{{ route('users.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add User</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->roles->pluck('name')->join(', ') ?: 'No role' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('users.show', $user) }}" class="font-medium text-gray-700 hover:text-gray-900">View</a>
                                @if (auth()->user()->hasPermission('users.manage'))
                                    <a href="{{ route('users.edit', $user) }}" class="ml-3 font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="ml-3 inline" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-medium text-red-600 hover:text-red-800" @disabled(auth()->id() === $user->id)>Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $users->links() }}</div>
    </div>
@endsection
