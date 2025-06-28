@extends('layouts.admin')

@section('title', 'Feature Coming Soon')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 text-center">
                <div class="mb-8">
                    <div class="text-6xl mb-4">🚧</div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Feature Coming Soon</h1>
                    <p class="text-gray-600 text-lg">This functionality is currently under development</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold text-blue-800 mb-3">Available Features</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-green-600 mb-2">✅ Analytics Dashboard</h3>
                            <p class="text-sm text-gray-600">View comprehensive analytics and insights</p>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-green-600 mb-2">✅ E-commerce Trends</h3>
                            <p class="text-sm text-gray-600">Monitor plant market trends and pricing</p>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-green-600 mb-2">✅ Site Configuration</h3>
                            <p class="text-sm text-gray-600">Configure and validate scraping sites</p>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <h3 class="font-semibold text-green-600 mb-2">✅ Account Management</h3>
                            <p class="text-sm text-gray-600">Manage store and admin accounts</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Quick Navigation</h3>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.trends.index') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">
                            🌱 E-commerce Trends
                        </a>
                        <a href="{{ route('admin.trends.configure') }}" class="bg-lime-600 hover:bg-lime-700 text-white px-4 py-2 rounded">
                            ⚙️ Configure Sites
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">
                            Analytics
                        </a>
                        <a href="{{ route('admin.accounts.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                            Accounts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
