@extends('layouts.admin')

@section('page-title', 'Reports')

@section('content')
    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($reports as $slug => $title)
            <a href="{{ route('reports.show', $slug) }}" class="rounded-lg border border-gray-200 bg-white p-5 font-semibold text-gray-900 shadow-sm hover:border-blue-300 hover:bg-blue-50">
                {{ $title }}
            </a>
        @endforeach
    </div>
@endsection
