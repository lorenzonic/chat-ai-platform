@extends('layouts.admin')

@section('title', 'Create QR Code')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Create New QR Code</h1>
                    <a href="{{ route('admin.qr-codes.index') }}"
                       class="text-gray-600 hover:text-gray-900">
                        ← Back to QR Codes
                    </a>
                </div>

                <form action="{{ route('admin.qr-codes.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="store_id" class="block text-sm font-medium text-gray-700">
                            Store *
                        </label>
                        <select id="store_id" name="store_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }} ({{ $store->slug }})
                                    @if($store->is_premium)
                                        - Premium
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('store_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            QR Code Name *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., Store Entrance QR, Product Catalog QR"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Give this QR code a descriptive name for internal reference.
                        </p>
                    </div>

                    <div>
                        <label for="question" class="block text-sm font-medium text-gray-700">
                            Pre-filled Question (Optional)
                        </label>
                        <textarea id="question" name="question" rows="3"
                                  placeholder="e.g., Che piante consigli per un appartamento poco luminoso?"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('question') }}</textarea>
                        @error('question')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            If set, this question will be automatically loaded when users scan the QR code.
                        </p>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-blue-900 mb-2">How it works:</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Users scan the QR code with their phone</li>
                            <li>• They're redirected to the store's chatbot page</li>
                            <li>• The pre-filled question (if any) appears automatically</li>
                            <li>• All interactions are tracked for analytics</li>
                        </ul>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.qr-codes.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
