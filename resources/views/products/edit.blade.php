@extends('layouts.admin')

@section('page-title', 'Edit Product')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @method('PUT')
            @include('products.form')
        </form>
    </div>
@endsection
