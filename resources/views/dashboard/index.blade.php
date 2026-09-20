@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <form method="GET" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-3">
            <select name="branch_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All branches</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((string)($filters['branch_id'] ?? '') === (string)$branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ $filters['date'] ?? now()->toDateString() }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Apply Filter</button>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $label => $value)
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                    <p class="mt-3 text-2xl font-bold text-gray-900">{{ is_numeric($value) ? number_format((float)$value, str_contains($label, 'Total') || str_contains($label, 'Sales') || str_contains($label, 'Due') || str_contains($label, 'Profit') || str_contains($label, 'Expenses') || str_contains($label, 'Purchases') ? 2 : 0) : $value }}</p>
                </div>
            @endforeach
        </div>

        <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Sales Last 30 Days</h2></div>
            <div class="flex h-56 items-end gap-2 p-5">
                @php $max = max((float) $salesChart->max('total'), 1); @endphp
                @forelse ($salesChart as $point)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="w-full rounded-t bg-blue-500" style="height: {{ max(((float)$point->total / $max) * 180, 4) }}px"></div>
                        <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($point->label)->format('d') }}</span>
                    </div>
                @empty
                    <div class="w-full text-center text-sm text-gray-500">No sales data.</div>
                @endforelse
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Recent Sales</h2></div>
                <div class="overflow-x-auto"><table class="min-w-full text-sm"><tbody class="divide-y">@forelse($recentSales as $sale)<tr><td class="px-5 py-3">{{ $sale->invoice_no }}</td><td>{{ $sale->customer?->name ?? 'Walk-in' }}</td><td class="text-right pr-5">{{ number_format((float)$sale->total,2) }}</td></tr>@empty<tr><td class="p-5 text-center text-gray-500">No recent sales.</td></tr>@endforelse</tbody></table></div>
            </section>
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Low Stock Products</h2></div>
                <div class="overflow-x-auto"><table class="min-w-full text-sm"><tbody class="divide-y">@forelse($lowStockProducts as $stock)<tr><td class="px-5 py-3">{{ $stock->product->name }}</td><td>{{ $stock->product->sku }}</td><td class="text-right pr-5">{{ number_format((float)$stock->quantity,3) }}</td></tr>@empty<tr><td class="p-5 text-center text-gray-500">No low stock products.</td></tr>@endforelse</tbody></table></div>
            </section>
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Top Selling Products</h2></div>
                <div class="overflow-x-auto"><table class="min-w-full text-sm"><tbody class="divide-y">@forelse($topProducts as $row)<tr><td class="px-5 py-3">{{ $row->product?->name }}</td><td>{{ number_format((float)$row->qty,3) }}</td><td class="text-right pr-5">{{ number_format((float)$row->total,2) }}</td></tr>@empty<tr><td class="p-5 text-center text-gray-500">No top products.</td></tr>@endforelse</tbody></table></div>
            </section>
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Payment Method Summary</h2></div>
                <div class="overflow-x-auto"><table class="min-w-full text-sm"><tbody class="divide-y">@forelse($paymentMethods as $row)<tr><td class="px-5 py-3">{{ ucfirst($row->payment_method) }}</td><td class="text-right pr-5">{{ number_format((float)$row->total,2) }}</td></tr>@empty<tr><td class="p-5 text-center text-gray-500">No payments.</td></tr>@endforelse</tbody></table></div>
            </section>
        </div>
    </div>
@endsection
