<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Simple Header -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">Admin Panel</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/admin/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        <a href="/admin/orders" class="text-gray-600 hover:text-gray-900">Orders</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto px-4">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        console.log('Simple admin layout loaded');
    </script>
</body>
</html>
