@extends('store.layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Edit Knowledge Entry</h2>
                    <a href="{{ route('store.knowledge.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to Knowledge Base
                    </a>
                </div>

                <form method="POST" action="{{ route('store.knowledge.update', $knowledge) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                        <input
                            type="text"
                            id="question"
                            name="question"
                            value="{{ old('question', $knowledge->question) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter the question customers might ask..."
                            required
                        >
                        @error('question')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">Answer</label>
                        <textarea
                            id="answer"
                            name="answer"
                            rows="6"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter the answer to provide to customers..."
                            required
                        >{{ old('answer', $knowledge->answer) }}</textarea>
                        @error('answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                            Keywords (Optional)
                        </label>
                        <input
                            type="text"
                            id="keywords"
                            name="keywords"
                            value="{{ old('keywords', $knowledge->keywords) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter keywords separated by commas (e.g., hours, opening, schedule)"
                        >
                        <p class="mt-1 text-sm text-gray-500">
                            Add keywords to help the chatbot find this answer when customers ask related questions.
                        </p>
                        @error('keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $knowledge->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-600">Active (visible to chatbot)</span>
                        </label>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between">
                        <button
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded"
                        >
                            Update Entry
                        </button>

                        <button
                            type="button"
                            onclick="if(confirm('Are you sure you want to delete this knowledge entry?')) { document.getElementById('delete-form').submit(); }"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded"
                        >
                            Delete Entry
                        </button>
                    </div>
                </form>

                <!-- Delete Form (hidden) -->
                <form id="delete-form" method="POST" action="{{ route('store.knowledge.destroy', $knowledge) }}" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
