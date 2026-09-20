@extends('layouts.admin')

@section('page-title', 'Stock Transfers')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('stock-transfers.index') }}" class="flex gap-2">
                <input name="search" value="{{ $search }}" placeholder="Search transfer no" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-80">
                <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Search</button>
            </form>
            <a href="{{ route('stock-transfers.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">New Transfer</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Transfer No</th>
                        <th class="px-5 py-3">From</th>
                        <th class="px-5 py-3">To</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Created By</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($transfers as $transfer)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $transfer->transfer_no }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $transfer->fromBranch->name }} / {{ $transfer->fromWarehouse?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $transfer->toBranch->name }} / {{ $transfer->toWarehouse?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $transfer->transfer_date->format('Y-m-d') }}</td>
                            <td class="px-5 py-3"><span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">{{ ucfirst($transfer->status) }}</span></td>
                            <td class="px-5 py-3 text-gray-600">{{ $transfer->creator?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('stock-transfers.show', $transfer) }}" class="font-medium text-blue-600 hover:text-blue-800">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No transfers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $transfers->links() }}</div>
    </div>
@endsection
