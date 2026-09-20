@extends('layouts.admin')

@section('page-title', 'Suppliers')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-2">
                <input name="search" value="{{ $search }}" placeholder="Search suppliers" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-80">
                <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Search</button>
            </form>
            <a href="{{ route('suppliers.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add Supplier</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Company</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Opening Due</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $supplier->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $supplier->company_name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $supplier->phone ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $supplier->email ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $supplier->opening_due, 2) }}</td>
                            <td class="px-5 py-3">@include('products.partials.status', ['status' => $supplier->status])</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="ml-3 inline" onsubmit="return confirm('Delete this supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-medium text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No suppliers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $suppliers->links() }}</div>
    </div>
@endsection
