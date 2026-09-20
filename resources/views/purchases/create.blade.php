@extends('layouts.admin')

@section('page-title', 'Create Purchase')

@section('content')
    @include('products.partials.alerts')

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('purchases.store') }}">
            @include('purchases.form')
        </form>
    </div>
@endsection
