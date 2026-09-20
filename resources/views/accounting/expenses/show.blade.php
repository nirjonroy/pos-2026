@extends('layouts.admin')
@section('page-title', 'Expense Details')
@section('content')
<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="grid gap-4 md:grid-cols-3 text-sm">
        <div><p class="text-gray-500">Expense No</p><p class="font-semibold">{{ $expense->expense_no }}</p></div>
        <div><p class="text-gray-500">Date</p><p class="font-semibold">{{ $expense->expense_date->format('Y-m-d') }}</p></div>
        <div><p class="text-gray-500">Amount</p><p class="font-semibold">{{ number_format((float)$expense->amount, 2) }}</p></div>
        <div><p class="text-gray-500">Category</p><p class="font-semibold">{{ $expense->category->name }}</p></div>
        <div><p class="text-gray-500">Branch</p><p class="font-semibold">{{ $expense->branch->name }}</p></div>
        <div><p class="text-gray-500">Payment Method</p><p class="font-semibold">{{ ucfirst($expense->payment_method) }}</p></div>
        <div><p class="text-gray-500">Reference No</p><p class="font-semibold">{{ $expense->reference_no ?? '-' }}</p></div>
        <div><p class="text-gray-500">Created By</p><p class="font-semibold">{{ $expense->creator?->name ?? '-' }}</p></div>
        <div class="md:col-span-3"><p class="text-gray-500">Description</p><p class="font-semibold">{{ $expense->description ?? '-' }}</p></div>
    </div>
</div>
@endsection
