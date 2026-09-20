@extends('layouts.admin')

@section('page-title', 'Purchases')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('purchases.index') }}" class="grid gap-2 sm:grid-cols-3">
                <input name="search" value="{{ $search }}" placeholder="Purchase no or supplier" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All statuses</option>
                    @foreach (['draft', 'received', 'cancelled'] as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
                <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Filter</button>
            </form>
            <a href="{{ route('purchases.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">New Purchase</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Purchase No</th>
                        <th class="px-5 py-3">Supplier</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Due</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $purchase->purchase_no }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $purchase->supplier->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $purchase->branch->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $purchase->total, 2) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $purchase->due_amount, 2) }}</td>
                            <td class="px-5 py-3"><span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ ucfirst($purchase->status) }}</span></td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('purchases.show', $purchase) }}" class="font-medium text-blue-600 hover:text-blue-800">View</a>
                                @if ($purchase->status === 'draft')
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="ml-3 font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-500">No purchases found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $purchases->links() }}</div>
    </div>
@endsection
