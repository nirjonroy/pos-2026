@extends('layouts.admin')
@section('page-title', 'Edit Expense Category')
@section('content')
@include('products.partials.alerts')
<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"><form method="POST" action="{{ route('expense-categories.update', $expenseCategory) }}">@method('PUT')@include('accounting.expense-categories.form')</form></div>
@endsection
