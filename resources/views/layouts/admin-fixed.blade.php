<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                            Admin Panel
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-1 sm:-my-px sm:ml-10 sm:flex sm:items-center">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors duration-200 border-gray-300">
                            <i class="fas fa-chart-line mr-2"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 {{ request()->routeIs('admin.orders.*') ? 'text-indigo-900 border-black' : 'text-gray-700 hover:bg-gray-50 border-gray-300' }} font-medium text-sm transition-colors duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Ordini
                        </a>
                        <a href="{{ route('admin.products.index') }}" 
                           class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 {{ request()->routeIs('admin.products.*') ? 'text-indigo-900 border-black' : 'text-gray-700 hover:bg-gray-50 border-gray-300' }} font-medium text-sm transition-colors duration-200">
                            <i class="fas fa-box mr-2"></i>
                            Prodotti
                        </a>
                        <a href="{{ route('admin.stores.index') }}" 
                           class="flex items-center justify-center border-2 rounded-3xl px-4 py-1 {{ request()->routeIs('admin.stores.*') ? 'text-indigo-900 border-black' : 'text-gray-700 hover:bg-gray-50 border-gray-300' }} font-medium text-sm transition-colors duration-200">
                            <i class="fas fa-store mr-2"></i>
                            Negozi
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    @auth
                    <div class="relative">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <i class="fas fa-user mr-2"></i>
                            {{ Auth::user()->name }}
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script>
        // Basic admin functionality
        console.log('Admin panel loaded');
    </script>
</body>
</html>
