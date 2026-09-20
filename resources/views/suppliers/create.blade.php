@extends('layouts.admin')

@section('page-title', 'Create Supplier')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @include('suppliers.form')
        </form>
    </div>
@endsection
