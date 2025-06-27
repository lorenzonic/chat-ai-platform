<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @auth('admin')
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                                    Admin Panel
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <a href="{{ route('admin.dashboard') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.dashboard') ? 'border-indigo-500 text-gray-900' : '' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.accounts.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.accounts.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                                    Accounts
                                </a>
                                <a href="{{ route('admin.qr-codes.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.qr-codes.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                                    QR Codes
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <span class="text-gray-700 mr-4">{{ auth('admin')->user()->name }}</span>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mr-4">
                                {{ ucfirst(str_replace('_', ' ', auth('admin')->user()->role)) }}
                            </span>
                            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
