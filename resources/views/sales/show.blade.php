@extends('layouts.admin')

@section('page-title', 'Sale Details')

@section('content')
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="grid flex-1 gap-4 md:grid-cols-3">
                    <div><p class="text-sm text-gray-500">Invoice</p><p class="font-semibold">{{ $sale->invoice_no }}</p></div>
                    <div><p class="text-sm text-gray-500">Date</p><p class="font-semibold">{{ $sale->sale_date->format('Y-m-d H:i') }}</p></div>
                    <div><p class="text-sm text-gray-500">Status</p><p class="font-semibold">{{ ucfirst($sale->status) }}</p></div>
                    <div><p class="text-sm text-gray-500">Customer</p><p class="font-semibold">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</p></div>
                    <div><p class="text-sm text-gray-500">Branch / Warehouse</p><p class="font-semibold">{{ $sale->branch->name }} / {{ $sale->warehouse?->name ?? '-' }}</p></div>
                    <div><p class="text-sm text-gray-500">Cashier</p><p class="font-semibold">{{ $sale->creator?->name ?? '-' }}</p></div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('sales.receipt', $sale) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Receipt</a>
                    @if ($sale->status !== 'cancelled')
                        <a href="{{ route('sales.returns.create', $sale) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Return</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Items</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Unit Price</th>
                            <th class="px-5 py-3">Discount</th>
                            <th class="px-5 py-3">Tax</th>
                            <th class="px-5 py-3 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($sale->items as $item)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $item->quantity, 3) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $item->discount, 2) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $item->tax, 2) }}</td>
                                <td class="px-5 py-3 text-right text-gray-600">{{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="grid gap-2 border-t border-gray-200 px-5 py-4 text-sm md:grid-cols-3">
                <div>Subtotal: <strong>{{ number_format((float) $sale->subtotal, 2) }}</strong></div>
                <div>Discount: <strong>{{ number_format((float) $sale->discount, 2) }}</strong></div>
                <div>Tax: <strong>{{ number_format((float) $sale->tax, 2) }}</strong></div>
                <div>Total: <strong>{{ number_format((float) $sale->total, 2) }}</strong></div>
                <div>Paid: <strong>{{ number_format((float) $sale->paid_amount, 2) }}</strong></div>
                <div>Due: <strong>{{ number_format((float) $sale->due_amount, 2) }}</strong></div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Payment Methods</div>
                <div class="p-5">
                    @forelse ($sale->payments as $payment)
                        <div class="flex justify-between border-b border-gray-100 py-2 text-sm">
                            <span>{{ ucfirst($payment->payment_method) }} - {{ $payment->paid_at->format('Y-m-d H:i') }}</span>
                            <strong>{{ number_format((float) $payment->amount, 2) }}</strong>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No payments recorded.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Returns</div>
                <div class="p-5">
                    @forelse ($sale->returns as $return)
                        <div class="border-b border-gray-100 py-2 text-sm">
                            <div class="flex justify-between">
                                <a href="{{ route('sale-returns.show', $return) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $return->return_no }}</a>
                                <strong>{{ number_format((float) $return->total_amount, 2) }}</strong>
                            </div>
                            <p class="text-xs text-gray-500">{{ $return->return_date->format('Y-m-d H:i') }} - {{ ucfirst($return->status) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No returns recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
