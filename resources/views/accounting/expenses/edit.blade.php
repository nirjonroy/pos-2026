@extends('layouts.admin')
@section('page-title', 'Edit Expense')
@section('content')
@include('products.partials.alerts')
<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"><form method="POST" action="{{ route('expenses.update', $expense) }}">@method('PUT')@include('accounting.expenses.form')</form></div>
@endsection
