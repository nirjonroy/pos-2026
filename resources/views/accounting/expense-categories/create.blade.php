@extends('layouts.admin')
@section('page-title', 'Create Expense Category')
@section('content')
@include('products.partials.alerts')
<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"><form method="POST" action="{{ route('expense-categories.store') }}">@include('accounting.expense-categories.form')</form></div>
@endsection
