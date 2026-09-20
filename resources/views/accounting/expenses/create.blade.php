@extends('layouts.admin')
@section('page-title', 'Create Expense')
@section('content')
@include('products.partials.alerts')
<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"><form method="POST" action="{{ route('expenses.store') }}">@include('accounting.expenses.form')</form></div>
@endsection
