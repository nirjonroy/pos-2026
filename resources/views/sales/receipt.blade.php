@extends('layouts.admin')

@section('page-title', 'Sales Receipt')

@section('content')
    <div class="mx-auto max-w-4xl space-y-4">
        <div class="flex justify-end print:hidden">
            <button type="button" onclick="window.print()" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Print</button>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-200 pb-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ config('app.name', 'POS') }}</h2>
                    <p class="text-sm text-gray-500">{{ $sale->branch->name }}</p>
                </div>
                <div class="text-sm sm:text-right">
                    <p><span class="text-gray-500">Invoice:</span> <strong>{{ $sale->invoice_no }}</strong></p>
                    <p><span class="text-gray-500">Date:</span> {{ $sale->sale_date->format('Y-m-d H:i') }}</p>
                    <p><span class="text-gray-500">Cashier:</span> {{ $sale->creator?->name ?? '-' }}</p>
                </div>
            </div>

            <div class="grid gap-3 py-4 text-sm sm:grid-cols-2">
                <p><span class="text-gray-500">Customer:</span> {{ $sale->customer?->name ?? 'Walk-in Customer' }}</p>
                <p><span class="text-gray-500">Branch:</span> {{ $sale->branch->name }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Unit Price</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($sale->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->product->name }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $item->quantity, 3) }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 grid gap-4 border-t border-gray-200 pt-4 text-sm md:grid-cols-2">
                <div>
                    <h3 class="mb-2 font-semibold text-gray-900">Payments</h3>
                    @forelse ($sale->payments as $payment)
                        <div class="flex justify-between border-b border-gray-100 py-1">
                            <span>{{ ucfirst($payment->payment_method) }}</span>
                            <strong>{{ number_format((float) $payment->amount, 2) }}</strong>
                        </div>
                    @empty
                        <p class="text-gray-500">No payment recorded.</p>
                    @endforelse
                </div>

                <div class="space-y-1 md:ml-auto md:w-72">
                    <div class="flex justify-between"><span>Subtotal</span><strong>{{ number_format((float) $sale->subtotal, 2) }}</strong></div>
                    <div class="flex justify-between"><span>Discount</span><strong>{{ number_format((float) $sale->discount, 2) }}</strong></div>
                    <div class="flex justify-between"><span>Tax</span><strong>{{ number_format((float) $sale->tax, 2) }}</strong></div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span>Total</span><strong>{{ number_format((float) $sale->total, 2) }}</strong></div>
                    <div class="flex justify-between"><span>Paid</span><strong>{{ number_format((float) $sale->paid_amount, 2) }}</strong></div>
                    <div class="flex justify-between"><span>Due</span><strong>{{ number_format((float) $sale->due_amount, 2) }}</strong></div>
                </div>
            </div>
        </div>
    </div>
@endsection
