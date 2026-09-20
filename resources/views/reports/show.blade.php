@extends('layouts.admin')

@section('page-title', $title)

@section('content')
    <div class="space-y-5">
        <form method="GET" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm lg:grid-cols-6 print:hidden">
            <input type="date" name="from_date" value="{{ request('from_date', $from) }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <input type="date" name="to_date" value="{{ request('to_date', $to) }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">

            <select name="branch_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All branches</option>
                @foreach ($filters['branches'] as $branch)
                    <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>

            <select name="product_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All products</option>
                @foreach ($filters['products'] as $product)
                    <option value="{{ $product->id }}" @selected((string) request('product_id') === (string) $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>

            <select name="payment_method" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All methods</option>
                @foreach (['cash','card','bkash','nagad','bank','store_credit'] as $method)
                    <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply Filter</button>
                <a href="{{ route('reports.show', $slug) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
                <button type="button" onclick="window.print()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Print</button>
            </div>
        </form>

        @if (! empty($totals))
            <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-4">
                @foreach ($totals as $label => $value)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="mt-2 text-xl font-bold text-gray-900">{{ is_numeric($value) ? number_format((float) $value, 2) : $value }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4">
                <h2 class="font-semibold text-gray-900">{{ $title }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            @foreach ($headers as $header)
                                <th class="px-5 py-3">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rows as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td class="px-5 py-3 text-gray-700">{{ is_numeric($cell) ? number_format((float) $cell, 2) : $cell }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($headers) ?: 1 }}" class="px-5 py-8 text-center text-gray-500">No report data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="border-t border-gray-200 px-5 py-4">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
@endsection
