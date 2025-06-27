@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Total Stores</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Store::count() }}</p>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-900 mb-2">Active Stores</h3>
                        <p class="text-3xl font-bold text-green-600">{{ \App\Models\Store::where('is_active', true)->count() }}</p>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Premium Stores</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ \App\Models\Store::where('is_premium', true)->count() }}</p>
                    </div>

                    <div class="bg-orange-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-orange-900 mb-2">QR Codes</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ \App\Models\QrCode::count() }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.accounts.stores.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center">
                            Create Store Account
                        </a>
                        <a href="{{ route('admin.accounts.admins.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-center">
                            Create Admin Account
                        </a>
                        <a href="{{ route('admin.accounts.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-center">
                            Manage Accounts
                        </a>
                        <a href="{{ route('admin.qr-codes.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-center">
                            Generate QR Code
                        </a>
                        <a href="{{ route('admin.qr-codes.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-center">
                            Manage QR Codes
                        </a>
                        <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">
                            Create Blog Post
                        </button>
                        <button class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded">
                            View Analytics
                        </button>
                        <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            System Settings
                        </button>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-4">Recent Stores</h2>
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
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
</div>
@endsection
