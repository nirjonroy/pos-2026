@extends('layouts.admin')

@section('page-title', 'Edit Purchase')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('purchases.update', $purchase) }}">
            @method('PUT')
            @include('purchases.form')
        </form>
    </div>
@endsection
