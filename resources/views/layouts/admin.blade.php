<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'POS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <style>[x-cloak] { display: none !important; }</style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-800">
        <div x-data="{ sidebarOpen: false, userMenuOpen: false }" class="min-h-screen">
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" x-cloak>
                <div class="fixed inset-0 bg-gray-900/50" @click="sidebarOpen = false"></div>
                <div class="fixed inset-y-0 left-0 w-72">
                    @include('partials.sidebar', ['mobile' => true])
                </div>
            </div>

            <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 lg:block">
                @include('partials.sidebar', ['mobile' => false])
            </aside>

            <div class="lg:pl-64">
                @include('partials.header')

                <main class="min-h-[calc(100vh-8rem)] px-4 py-6 sm:px-6 lg:px-8">
                    @yield('content')
                </main>

                @include('partials.footer')
            </div>
        </div>
    </body>
</html>
