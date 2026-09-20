@extends('layouts.admin')

@section('page-title', 'Stock Transfer Details')

@section('content')
    @include('products.partials.alerts')

    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div><p class="text-sm text-gray-500">Transfer No</p><p class="font-semibold">{{ $stockTransfer->transfer_no }}</p></div>
                <div><p class="text-sm text-gray-500">From</p><p class="font-semibold">{{ $stockTransfer->fromBranch->name }} / {{ $stockTransfer->fromWarehouse?->name ?? '-' }}</p></div>
                <div><p class="text-sm text-gray-500">To</p><p class="font-semibold">{{ $stockTransfer->toBranch->name }} / {{ $stockTransfer->toWarehouse?->name ?? '-' }}</p></div>
                <div><p class="text-sm text-gray-500">Date</p><p class="font-semibold">{{ $stockTransfer->transfer_date->format('Y-m-d') }}</p></div>
                <div><p class="text-sm text-gray-500">Status</p><p class="font-semibold">{{ ucfirst($stockTransfer->status) }}</p></div>
                <div><p class="text-sm text-gray-500">Created By</p><p class="font-semibold">{{ $stockTransfer->creator?->name ?? '-' }}</p></div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Items</div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr><th class="px-5 py-3">Product</th><th class="px-5 py-3">Quantity</th><th class="px-5 py-3">Unit Cost</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($stockTransfer->items as $item)
                        <tr>
                            <td class="px-5 py-3">{{ $item->product->name }}</td>
                            <td class="px-5 py-3">{{ number_format((float) $item->quantity, 3) }}</td>
                            <td class="px-5 py-3">{{ number_format((float) $item->unit_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
