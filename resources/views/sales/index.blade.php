@extends('layouts.admin')

@section('page-title', 'Sales')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('sales.index') }}" class="grid gap-3 border-b border-gray-200 px-5 py-4 lg:grid-cols-6">
            <input name="search" value="{{ $search }}" placeholder="Invoice or customer" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 lg:col-span-2">
            <input name="date_from" type="date" value="{{ $dateFrom }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <input name="date_to" type="date" value="{{ $dateTo }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach (['completed', 'partial', 'cancelled'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <select name="branch_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All branches</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((string) $branchId === (string) $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
            <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 lg:col-span-6">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Invoice</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Paid</th>
                        <th class="px-5 py-3">Due</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Cashier</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($sales as $sale)
                        @php
                            $statusClass = $sale->status === 'completed' ? 'bg-green-100 text-green-700' : ($sale->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                        @endphp
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $sale->invoice_no }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $sale->branch->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $sale->total, 2) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $sale->paid_amount, 2) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ number_format((float) $sale->due_amount, 2) }}</td>
                            <td class="px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($sale->status) }}</span></td>
                            <td class="px-5 py-3 text-gray-600">{{ $sale->creator?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('sales.show', $sale) }}" class="font-medium text-blue-600 hover:text-blue-800">View</a>
                                <a href="{{ route('sales.receipt', $sale) }}" class="ml-3 font-medium text-blue-600 hover:text-blue-800">Receipt</a>
                                @if ($sale->status !== 'cancelled')
                                    <a href="{{ route('sales.returns.create', $sale) }}" class="ml-3 font-medium text-blue-600 hover:text-blue-800">Return</a>
                                @else
                                    <span class="ml-3 font-medium text-gray-400">Return</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-8 text-center text-gray-500">No sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $sales->links() }}</div>
    </div>
@endsection
