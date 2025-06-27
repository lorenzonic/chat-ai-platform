@extends('layouts.admin')

@section('title', 'Store Details - ' . $store->name)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Store Details: {{ $store->name }}</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.accounts.stores.edit', $store) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Edit Store
                        </a>
                        <a href="{{ route('admin.accounts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Back to Accounts
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Store Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Store Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Store Slug</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">/{{ $store->slug }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->phone ?: 'Not provided' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->description ?: 'No description provided' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Website</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($store->website)
                                            <a href="{{ $store->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                {{ $store->website }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->city ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->address ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Country</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->country ?: 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Account Dates</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->created_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $store->updated_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status and Actions -->
                    <div>
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Account Status</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <div class="mt-1">
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                                                   {{ $store->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $store->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plan</label>
                                    <div class="mt-1">
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                                                   {{ $store->is_premium ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $store->is_premium ? 'Premium' : 'Standard' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <form method="POST" action="{{ route('admin.accounts.stores.toggle-status', $store) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium rounded
                                                                 {{ $store->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                        {{ $store->is_active ? 'Deactivate Account' : 'Activate Account' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.accounts.stores.toggle-premium', $store) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium rounded
                                                                 {{ $store->is_premium ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
                                        {{ $store->is_premium ? 'Remove Premium' : 'Make Premium' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.accounts.stores.edit', $store) }}"
                                   class="block w-full px-4 py-2 text-sm font-medium text-center bg-blue-100 text-blue-800 hover:bg-blue-200 rounded">
                                    Edit Account
                                </a>
                            </div>
                        </div>

                        <div class="bg-red-50 p-6 rounded-lg border border-red-200">
                            <h3 class="text-lg font-semibold mb-4 text-red-800">Danger Zone</h3>
                            <p class="text-sm text-red-700 mb-4">
                                Once you delete an account, there is no going back. This will permanently delete all store data.
                            </p>
                            <form method="POST" action="{{ route('admin.accounts.stores.destroy', $store) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this store account? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 text-sm font-medium bg-red-600 text-white hover:bg-red-700 rounded">
                                    Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Store URLs -->
                <div class="mt-8 bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Store URLs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Store Dashboard</label>
                            <p class="mt-1">
                                <a href="{{ url('/store/login') }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    {{ url('/store/login') }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Chatbot Page</label>
                            <p class="mt-1">
                                <a href="{{ url('/' . $store->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    {{ url('/' . $store->slug) }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
