@extends('layouts.admin')

@section('page-title', 'Create Brand')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('brands.store') }}">
            @include('products.brands.form')
        </form>
    </div>
@endsection
