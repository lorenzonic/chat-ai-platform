@extends('layouts.admin')

@section('title', __('admin.grower_details'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">
                        <i class="fas fa-seedling mr-2 text-green-600"></i>{{ $grower->name }}
                    </h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.accounts.growers.edit', $grower) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-edit mr-2"></i>{{ __('common.edit') }}
                        </a>
                        <a href="{{ route('admin.accounts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>{{ __('common.back') }}
                        </a>
                    </div>
                </div>

                <!-- Status Alert -->
                @if(!$grower->is_active)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ __('auth.account_inactive') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Grower Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.grower_info') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.name') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('admin.grower_code') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->code ?: __('common.no_data') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.email') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.phone') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->phone ?: __('common.no_data') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.address') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->address ?: __('common.no_data') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.city') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->city ?: __('common.no_data') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.country') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->country ?: __('common.no_data') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('common.status') }}</label>
                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $grower->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $grower->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </div>
                            </div>
                            @if($grower->notes)
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-500">Notes</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $grower->notes }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Products Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-seedling mr-2"></i>Products ({{ $grower->products()->count() }})
                                </h3>
                            </div>

                            @if($grower->products()->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($grower->products()->latest()->take(10)->get() as $product)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $product->name }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $product->code }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">€{{ number_format($product->price, 2) }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $product->stock_quantity }} {{ $product->unit }}</td>
                                                    <td class="px-4 py-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($grower->products()->count() > 10)
                                    <p class="mt-2 text-sm text-gray-500">Showing first 10 products of {{ $grower->products()->count() }} total.</p>
                                @endif
                            @else
                                <p class="text-gray-500 text-center py-4">No products added yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Products:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $grower->products()->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Active Products:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $grower->products()->where('is_active', true)->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Orders:</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ \App\Models\Order::whereHas('products', function($query) use ($grower) { $query->where('grower_id', $grower->id); })->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Account Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Details</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Created:</span>
                                    <p class="font-medium">{{ $grower->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Last Updated:</span>
                                    <p class="font-medium">{{ $grower->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Email Verified:</span>
                                    <p class="font-medium">{{ $grower->email_verified_at ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <form action="{{ route('admin.accounts.growers.toggle-status', $grower) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm {{ $grower->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded">
                                        <i class="fas {{ $grower->is_active ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                                        {{ $grower->is_active ? 'Deactivate' : 'Activate' }} Account
                                    </button>
                                </form>

                                <form action="{{ route('admin.accounts.growers.destroy', $grower) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this grower account? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm bg-red-100 text-red-700 hover:bg-red-200 rounded">
                                        <i class="fas fa-trash mr-2"></i>Delete Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
