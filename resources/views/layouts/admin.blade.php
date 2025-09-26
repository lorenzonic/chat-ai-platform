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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                                    🏠 Dashboard
                                </a>
                                <a href="{{ route('admin.trends.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.trends.*') ? 'border-emerald-500 text-emerald-900' : '' }}">
                                    🌱 Plant Trends
                                </a>
                                <a href="{{ route('admin.trending-keywords.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.trending-keywords.*') ? 'border-green-500 text-green-900' : '' }}">
                                    🔍 Google Trends
                                </a>
                                <a href="{{ route('admin.analytics.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.analytics.*') ? 'border-teal-500 text-teal-900' : '' }}">
                                    📊 Analytics
                                </a>
                                <a href="{{ route('admin.accounts.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.accounts.*') ? 'border-blue-500 text-blue-900' : '' }}">
                                    👥 Accounts
                                </a>
                                <a href="{{ route('admin.qr-codes.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.qr-codes.*') ? 'border-purple-500 text-purple-900' : '' }}">
                                    📱 QR Codes
                                </a>
                                <a href="{{ route('admin.products.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.products.*') ? 'border-orange-500 text-orange-900' : '' }}">
                                    🏷️ Products-Stickers
                                </a>
                                <a href="{{ route('admin.orders.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.orders.*') ? 'border-red-500 text-red-900' : '' }}">
                                    📦 Orders
                                </a>
                                <a href="{{ route('admin.order-items.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.order-items.*') ? 'border-blue-500 text-blue-900' : '' }}">
                                    📋 Order Items
                                </a>
                                <a href="{{ route('admin.growers.index') }}"
                                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                          {{ request()->routeIs('admin.growers.*') ? 'border-green-500 text-green-900' : '' }}">
                                    🌱 Growers
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

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">🌱 Plant Analytics Platform</span>
                        <span class="text-xs text-gray-400">v1.0.0</span>
                    </div>
                    <div class="flex space-x-6">
                        <a href="{{ route('admin.trends.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">
                            Trends Dashboard
                        </a>
                        <a href="{{ route('admin.trends.configure') }}" class="text-sm text-gray-500 hover:text-purple-600">
                            Configure Sites
                        </a>
                        <a href="{{ url('/test/navigation') }}" class="text-sm text-gray-500 hover:text-blue-600">
                            Test Navigation
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>
