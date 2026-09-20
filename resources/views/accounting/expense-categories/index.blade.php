@extends('layouts.admin')
@section('page-title', 'Expense Categories')
@section('content')
@include('products.partials.alerts')
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex gap-2">
            <input name="search" value="{{ $search }}" placeholder="Search categories" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Search</button>
        </form>
        <a href="{{ route('expense-categories.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add Category</a>
    </div>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $category)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $category->name }}</td>
                    <td class="px-5 py-3">@include('products.partials.status', ['status' => $category->status])</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('expense-categories.edit', $category) }}" class="font-medium text-blue-600">Edit</a>
                        <form method="POST" action="{{ route('expense-categories.destroy', $category) }}" class="ml-3 inline" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="font-medium text-red-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-gray-200 px-5 py-4">{{ $categories->links() }}</div>
</div>
@endsection
