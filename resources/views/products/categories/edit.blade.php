@extends('layouts.admin')

@section('page-title', 'Edit Category')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @method('PUT')
            @include('products.categories.form')
        </form>
    </div>
@endsection
