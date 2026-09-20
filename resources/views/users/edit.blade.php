@extends('layouts.admin')

@section('page-title', 'Edit User')

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @method('PUT')
            @include('users.form')
        </form>
    </div>
@endsection
