@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-gray-600">Overview and management of the platform</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">S</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Total Stores</h3>
                            <p class="text-2xl font-bold text-gray-700">{{ \App\Models\Store::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">A</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Active Stores</h3>
                            <p class="text-2xl font-bold text-gray-700">{{ \App\Models\Store::where('is_active', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">P</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Premium Stores</h3>
                            <p class="text-2xl font-bold text-gray-700">{{ \App\Models\Store::where('is_premium', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">Q</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">QR Codes</h3>
                            <p class="text-2xl font-bold text-gray-700">{{ \App\Models\QrCode::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.accounts.stores.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        Create Store Account
                    </a>
                    <a href="{{ route('admin.accounts.admins.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        Create Admin Account
                    </a>
                    <a href="{{ route('admin.accounts.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        Manage Accounts
                    </a>
                    <a href="{{ route('admin.qr-codes.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        Generate QR Code
                    </a>
                    <a href="{{ route('admin.qr-codes.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        Manage QR Codes
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        View Analytics
                    </a>
                    <a href="{{ route('admin.trends.index') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        E-commerce Trends
                    </a>
                    <a href="{{ route('admin.trends.configure') }}" class="bg-lime-600 hover:bg-lime-700 text-white px-4 py-2 rounded text-center text-sm font-medium">
                        Configure Sites
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Stores -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Recent Stores</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Slug
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse(\App\Models\Store::latest()->take(5)->get() as $store)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $store->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $store->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    /{{ $store->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                               {{ $store->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $store->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($store->is_premium)
                                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Premium
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $store->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.accounts.stores.show', $store) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a href="{{ route('admin.accounts.stores.edit', $store) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No stores registered yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
