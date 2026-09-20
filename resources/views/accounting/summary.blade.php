@extends('layouts.admin')
@section('page-title', 'Accounting Summary')
@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    @foreach([
        'Total Sales' => $totalSales,
        'Total Purchase' => $totalPurchase,
        'Total Expenses' => $totalExpenses,
        'Sales Refunds' => $salesRefunds,
        'Purchase Due' => $purchaseDue,
        'Sales Due' => $salesDue,
        'Net Cash Flow' => $netCashFlow,
    ] as $label => $value)
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format((float)$value, 2) }}</p>
        </div>
    @endforeach
</div>
@endsection
