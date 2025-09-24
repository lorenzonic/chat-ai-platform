@extends('layouts.grower')

@section('title', 'Edit Product: ' . $product->name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Edit Product</h1>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-500">
                            {{ $grower->company_name }}
                        </div>
                        <a href="{{ route('grower.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Back to Products
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('grower.products.update', $product) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               required maxlength="255">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                  maxlength="1000" placeholder="Optional product description...">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                    </div>

                    <!-- EAN Code -->
                    <div>
                        <label for="ean" class="block text-sm font-medium text-gray-700">EAN Code</label>
                        <input type="text" name="ean" id="ean" value="{{ old('ean', $product->ean) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('ean') border-red-500 @enderror"
                               maxlength="20" placeholder="e.g., 8051277781620">
                        @error('ean')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">European Article Number (barcode identifier)</p>
                    </div>

                    <!-- Price and Quantity in a row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price (€)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}"
                                       class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity in Stock</label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('quantity') border-red-500 @enderror"
                                   min="0" placeholder="0">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Product Information (Read-only) -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Current Product Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Product Code:</span>
                                <span class="ml-2 font-mono bg-gray-200 px-2 py-1 rounded">{{ $product->code }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Category:</span>
                                <span class="ml-2">{{ $product->category }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Height:</span>
                                <span class="ml-2">{{ $product->height ? $product->height . ' cm' : 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Client:</span>
                                <span class="ml-2">{{ $product->client ?: 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Created:</span>
                                <span class="ml-2">{{ $product->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Last Updated:</span>
                                <span class="ml-2">{{ $product->updated_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('grower.products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                                View Product Details
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('grower.products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Additional Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Additional Actions</h3>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('grower.products.show', $product) }}" class="text-green-600 hover:text-green-800">
                            📄 View/Print Labels
                        </a>
                        <form method="POST" action="{{ route('grower.products.destroy', $product) }}" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                🗑️ Delete Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
