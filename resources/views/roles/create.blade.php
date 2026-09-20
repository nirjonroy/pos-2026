@extends('layouts.admin')

@section('page-title', 'Add Role')

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('roles.store') }}">
            @include('roles.form')
        </form>
    </div>
@endsection
