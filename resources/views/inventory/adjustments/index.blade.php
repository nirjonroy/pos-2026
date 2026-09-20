@extends('layouts.admin')

@section('page-title', 'Stock Adjustments')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('stock-adjustments.index') }}" class="flex gap-2">
                <input name="search" value="{{ $search }}" placeholder="Search adjustment no/reason" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-80">
                <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Search</button>
            </form>
            <a href="{{ route('stock-adjustments.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">New Adjustment</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Adjustment No</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3">Warehouse</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Created By</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($adjustments as $adjustment)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $adjustment->adjustment_no }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $adjustment->branch->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $adjustment->warehouse?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ ucfirst($adjustment->type) }}</td>
                            <td class="px-5 py-3"><span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">{{ ucfirst($adjustment->status) }}</span></td>
                            <td class="px-5 py-3 text-gray-600">{{ $adjustment->creator?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('stock-adjustments.show', $adjustment) }}" class="font-medium text-blue-600 hover:text-blue-800">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No adjustments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">{{ $adjustments->links() }}</div>
    </div>
@endsection
