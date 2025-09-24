<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Grower Portal')</title>

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
            <!-- Navigation -->
            @auth('grower')
                <nav class="bg-white border-b border-gray-200 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <a href="{{ route('grower.dashboard') }}" class="text-gray-900 text-xl font-bold">
                                        🌱 ChatAI Grower
                                    </a>
                                </div>

                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <a href="{{ route('grower.dashboard') }}"
                                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-200 ease-in-out
                                              {{ request()->routeIs('grower.dashboard') ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }}">
                                        🏠 Dashboard
                                    </a>
                                    <a href="{{ route('grower.products.index') }}"
                                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-200 ease-in-out
                                              {{ request()->routeIs('grower.products.*') ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }}">
                                        📦 My Products
                                    </a>
                                    <a href="{{ route('grower.orders.index') }}"
                                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-200 ease-in-out
                                              {{ request()->routeIs('grower.orders.*') ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }}">
                                        📋 My Orders
                                    </a>
                                </div>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <div class="relative">
                                    <button onclick="toggleDropdown()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-200 ease-in-out">
                                        <div>{{ auth('grower')->user()->contact_name ?? auth('grower')->user()->company_name ?? auth('grower')->user()->email ?? 'Grower' }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>

                                    <div id="dropdown" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <form method="POST" action="{{ route('grower.logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Log Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Hamburger Menu -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-900 transition duration-200 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div id="mobile-menu" class="hidden sm:hidden bg-white border-t border-gray-200">
                        <div class="pt-2 pb-3 space-y-1">
                            <a href="{{ route('grower.dashboard') }}"
                               class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition duration-200 ease-in-out
                                      {{ request()->routeIs('grower.dashboard') ? 'border-gray-900 text-gray-900 bg-gray-50' : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300' }}">
                                🏠 Dashboard
                            </a>
                            <a href="{{ route('grower.products.index') }}"
                               class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition duration-200 ease-in-out
                                      {{ request()->routeIs('grower.products.*') ? 'border-gray-900 text-gray-900 bg-gray-50' : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300' }}">
                                📦 My Products
                            </a>
                            <a href="{{ route('grower.orders.index') }}"
                               class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition duration-200 ease-in-out
                                      {{ request()->routeIs('grower.orders.*') ? 'border-gray-900 text-gray-900 bg-gray-50' : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300' }}">
                                📋 My Orders
                            </a>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="px-4">
                                <div class="font-medium text-base text-gray-900">{{ auth('grower')->user()->contact_name ?? auth('grower')->user()->company_name ?? auth('grower')->user()->email ?? 'Grower' }}</div>
                                <div class="font-medium text-sm text-gray-600">{{ auth('grower')->user()->email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <form method="POST" action="{{ route('grower.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300 transition duration-200 ease-in-out">
                                        Log Out
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

        <!-- JavaScript for dropdowns -->
        <script>
            function toggleDropdown() {
                const dropdown = document.getElementById('dropdown');
                dropdown.classList.toggle('hidden');
            }

            function toggleMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                mobileMenu.classList.toggle('hidden');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('dropdown');
                const button = event.target.closest('button');

                if (!button || !button.onclick) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
