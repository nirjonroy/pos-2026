@extends('layouts.admin')

@section('page-title', 'Purchase Details')

@section('content')
    @include('products.partials.alerts')

    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="grid flex-1 gap-4 md:grid-cols-3">
                    <div><p class="text-sm text-gray-500">Purchase No</p><p class="font-semibold">{{ $purchase->purchase_no }}</p></div>
                    <div><p class="text-sm text-gray-500">Supplier</p><p class="font-semibold">{{ $purchase->supplier->name }}</p></div>
                    <div><p class="text-sm text-gray-500">Status</p><p class="font-semibold">{{ ucfirst($purchase->status) }}</p></div>
                    <div><p class="text-sm text-gray-500">Branch</p><p class="font-semibold">{{ $purchase->branch->name }}</p></div>
                    <div><p class="text-sm text-gray-500">Warehouse</p><p class="font-semibold">{{ $purchase->warehouse?->name ?? '-' }}</p></div>
                    <div><p class="text-sm text-gray-500">Date</p><p class="font-semibold">{{ $purchase->purchase_date->format('Y-m-d') }}</p></div>
                </div>

                @if ($purchase->status === 'draft')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('purchases.receive', $purchase) }}" onsubmit="return confirm('Receive this purchase and update stock?')">
                            @csrf
                            <button class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Receive</button>
                        </form>
                        <form method="POST" action="{{ route('purchases.cancel', $purchase) }}" onsubmit="return confirm('Cancel this purchase?')">
                            @csrf
                            <button class="rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Cancel</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Items</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr><th class="px-5 py-3">Product</th><th class="px-5 py-3">Qty</th><th class="px-5 py-3">Unit Cost</th><th class="px-5 py-3">Discount</th><th class="px-5 py-3">Tax</th><th class="px-5 py-3">Total</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($purchase->items as $item)
                            <tr>
                                <td class="px-5 py-3">{{ $item->product->name }}</td>
                                <td class="px-5 py-3">{{ number_format((float) $item->quantity, 3) }}</td>
                                <td class="px-5 py-3">{{ number_format((float) $item->unit_cost, 2) }}</td>
                                <td class="px-5 py-3">{{ number_format((float) $item->discount, 2) }}</td>
                                <td class="px-5 py-3">{{ number_format((float) $item->tax, 2) }}</td>
                                <td class="px-5 py-3">{{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="grid gap-2 border-t border-gray-200 px-5 py-4 text-sm md:grid-cols-3">
                <div>Subtotal: <strong>{{ number_format((float) $purchase->subtotal, 2) }}</strong></div>
                <div>Discount: <strong>{{ number_format((float) $purchase->discount, 2) }}</strong></div>
                <div>Tax: <strong>{{ number_format((float) $purchase->tax, 2) }}</strong></div>
                <div>Total: <strong>{{ number_format((float) $purchase->total, 2) }}</strong></div>
                <div>Paid: <strong>{{ number_format((float) $purchase->paid_amount, 2) }}</strong></div>
                <div>Due: <strong>{{ number_format((float) $purchase->due_amount, 2) }}</strong></div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Payments</div>
                <div class="p-5">
                    @forelse ($purchase->payments as $payment)
                        <div class="flex justify-between border-b border-gray-100 py-2 text-sm">
                            <span>{{ ucfirst($payment->payment_method) }} - {{ $payment->paid_at->format('Y-m-d H:i') }}</span>
                            <strong>{{ number_format((float) $payment->amount, 2) }}</strong>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No payments yet.</p>
                    @endforelse
                </div>
            </div>

            @if ($purchase->due_amount > 0 && $purchase->status !== 'cancelled')
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-900">Add Payment</h2>
                    <form method="POST" action="{{ route('purchases.payments.store', $purchase) }}" class="grid gap-4">
                        @csrf
                        <select name="payment_method" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach (['cash', 'card', 'bkash', 'nagad', 'bank'] as $method)
                                <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                            @endforeach
                        </select>
                        <input name="amount" type="number" step="0.01" min="0.01" max="{{ $purchase->due_amount }}" placeholder="Amount" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <input name="reference_no" placeholder="Reference no" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <input name="paid_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" required class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Payment</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
