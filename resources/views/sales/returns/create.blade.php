@extends('layouts.admin')

@section('page-title', 'Create Sale Return')

@section('content')
    @include('products.partials.alerts')

    <form method="POST" action="{{ route('sales.returns.store', $sale) }}" class="space-y-6" onsubmit="return confirm('Complete this sale return?')">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div><p class="text-sm text-gray-500">Invoice</p><p class="font-semibold">{{ $sale->invoice_no }}</p></div>
                <div><p class="text-sm text-gray-500">Customer</p><p class="font-semibold">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</p></div>
                <div><p class="text-sm text-gray-500">Branch</p><p class="font-semibold">{{ $sale->branch->name }}</p></div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-900">Return Items</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Sold Qty</th>
                            <th class="px-5 py-3">Already Returned</th>
                            <th class="px-5 py-3">Returnable</th>
                            <th class="px-5 py-3">Return Qty</th>
                            <th class="px-5 py-3">Unit Price</th>
                            <th class="px-5 py-3">Refund Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($returnableItems as $index => $row)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $row['item']->product->name }}
                                    <input type="hidden" name="items[{{ $index }}][sale_item_id]" value="{{ $row['item']->id }}">
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format($row['sold_quantity'], 3) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format($row['returned_quantity'], 3) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format($row['returnable_quantity'], 3) }}</td>
                                <td class="px-5 py-3">
                                    <input name="items[{{ $index }}][quantity]" type="number" step="0.001" min="0" max="{{ $row['returnable_quantity'] }}" value="{{ old("items.$index.quantity", 0) }}" class="w-28 rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" @disabled($row['returnable_quantity'] <= 0)>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format((float) $row['item']->unit_price, 2) }}</td>
                                <td class="px-5 py-3 text-gray-600">Qty x {{ number_format((float) $row['item']->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Refund Method</label>
                    <select name="refund_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach (['cash', 'card', 'bkash', 'nagad', 'bank', 'store_credit'] as $method)
                            <option value="{{ $method }}" @selected(old('refund_method') === $method)>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                        @endforeach
                    </select>
                    @include('products.partials.field-error', ['name' => 'refund_method'])
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference No</label>
                    <input name="reference_no" value="{{ old('reference_no') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('reason') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Complete Return</button>
                <a href="{{ route('sales.show', $sale) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </div>
    </form>
@endsection
