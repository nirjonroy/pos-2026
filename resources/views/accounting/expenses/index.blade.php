@extends('layouts.admin')
@section('page-title', 'Expenses')
@section('content')
@include('products.partials.alerts')
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <form method="GET" class="grid gap-3 border-b border-gray-200 px-5 py-4 lg:grid-cols-6">
        <input name="search" value="{{ $search }}" placeholder="Expense no/description" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <input name="date_from" type="date" value="{{ $dateFrom }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <input name="date_to" type="date" value="{{ $dateTo }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <select name="expense_category_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)$categoryId===(string)$category->id)>{{ $category->name }}</option>@endforeach</select>
        <select name="branch_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"><option value="">All branches</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected((string)$branchId===(string)$branch->id)>{{ $branch->name }}</option>@endforeach</select>
        <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Filter</button>
    </form>
    <div class="flex justify-end border-b border-gray-200 px-5 py-3"><a href="{{ route('expenses.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add Expense</a></div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"><tr><th class="px-5 py-3">Expense No</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Branch</th><th class="px-5 py-3">Amount</th><th class="px-5 py-3">Payment Method</th><th class="px-5 py-3">Created By</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expenses as $expense)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $expense->expense_no }}</td>
                        <td class="px-5 py-3">{{ $expense->expense_date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3">{{ $expense->category->name }}</td>
                        <td class="px-5 py-3">{{ $expense->branch->name }}</td>
                        <td class="px-5 py-3">{{ number_format((float)$expense->amount, 2) }}</td>
                        <td class="px-5 py-3">{{ ucfirst($expense->payment_method) }}</td>
                        <td class="px-5 py-3">{{ $expense->creator?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('expenses.show', $expense) }}" class="font-medium text-blue-600">View</a><a href="{{ route('expenses.edit', $expense) }}" class="ml-3 font-medium text-blue-600">Edit</a><form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="ml-3 inline" onsubmit="return confirm('Delete this expense?')">@csrf @method('DELETE')<button class="font-medium text-red-600">Delete</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-8 text-center text-gray-500">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-200 px-5 py-4">{{ $expenses->links() }}</div>
</div>
@endsection
