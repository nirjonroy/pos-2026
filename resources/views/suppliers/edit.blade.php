@extends('layouts.admin')

@section('page-title', 'Edit Supplier')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @method('PUT')
            @include('suppliers.form')
        </form>
    </div>
@endsection
