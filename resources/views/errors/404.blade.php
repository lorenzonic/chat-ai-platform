@extends('layouts.admin')

@section('title', 'Page Not Found')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 text-center">
                <div class="mb-8">
                    <div class="text-9xl mb-4">🌵</div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">404 - Page Not Found</h1>
                    <p class="text-gray-600 text-lg">Questa pagina non è ancora germogliata nel nostro giardino digitale</p>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold text-green-800 mb-3">🌱 Navigate to Available Sections</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-blue-600 mb-2">📊 Dashboard</h3>
                            <p class="text-sm text-gray-600 mb-3">Main admin dashboard</p>
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 underline">Go to Dashboard</a>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-emerald-600 mb-2">🌱 Plant Trends</h3>
                            <p class="text-sm text-gray-600 mb-3">E-commerce analytics</p>
                            <a href="{{ route('admin.trends.index') }}" class="text-emerald-600 hover:text-emerald-800 underline">View Trends</a>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-purple-600 mb-2">⚙️ Configuration</h3>
                            <p class="text-sm text-gray-600 mb-3">Configure scraping sites</p>
                            <a href="{{ route('admin.trends.configure') }}" class="text-purple-600 hover:text-purple-800 underline">Configure Sites</a>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <a href="{{ route('admin.dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                        🏠 Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
