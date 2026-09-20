@extends('layouts.admin')

@section('page-title', 'Sale Return Details')

@section('content')
    @include('products.partials.alerts')

    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div><p class="text-sm text-gray-500">Return No</p><p class="font-semibold">{{ $saleReturn->return_no }}</p></div>
                <div><p class="text-sm text-gray-500">Original Invoice</p><p class="font-semibold"><a href="{{ route('sales.show', $saleReturn->sale) }}" class="text-blue-600 hover:text-blue-800">{{ $saleReturn->sale->invoice_no }}</a></p></div>
                <div><p class="text-sm text-gray-500">Date</p><p class="font-semibold">{{ $saleReturn->return_date->format('Y-m-d H:i') }}</p></div>
                <div><p class="text-sm text-gray-500">Cashier/User</p><p class="font-semibold">{{ $saleReturn->creator?->name ?? '-' }}</p></div>
                <div><p class="text-sm text-gray-500">Customer</p><p class="font-semibold">{{ $saleReturn->sale->customer?->name ?? 'Walk-in Customer' }}</p></div>
                <div><p class="text-sm text-gray-500">Refund Total</p><p class="font-semibold">{{ number_format((float) $saleReturn->total_amount, 2) }}</p></div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Returned Items</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Unit Price</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($saleReturn->items as $item)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $item->quantity, 3) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-5 py-3 text-right text-gray-600">{{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Refund Payment</div>
            <div class="p-5">
                @foreach ($saleReturn->refundPayments as $payment)
                    <div class="flex justify-between border-b border-gray-100 py-2 text-sm">
                        <span>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} - {{ $payment->refunded_at->format('Y-m-d H:i') }}</span>
                        <strong>{{ number_format((float) $payment->amount, 2) }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
