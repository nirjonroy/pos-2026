@extends('layouts.admin')

@section('page-title', 'Edit Role')

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @method('PUT')
            @include('roles.form')
        </form>
    </div>
@endsection
