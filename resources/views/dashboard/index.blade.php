@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Today's Sales</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">BDT 0.00</p>
                <p class="mt-2 text-xs text-gray-500">Placeholder value</p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total Sales</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">BDT 0.00</p>
                <p class="mt-2 text-xs text-gray-500">Placeholder value</p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Products</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">0</p>
                <p class="mt-2 text-xs text-gray-500">Placeholder value</p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Customers</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">0</p>
                <p class="mt-2 text-xs text-gray-500">Placeholder value</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Recent Sales</h2>
                </div>
                <div class="p-5">
                    <div class="rounded-md border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500">
                        Recent sales will appear here.
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Low Stock Products</h2>
                </div>
                <div class="p-5">
                    <div class="rounded-md border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500">
                        Low stock products will appear here.
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
