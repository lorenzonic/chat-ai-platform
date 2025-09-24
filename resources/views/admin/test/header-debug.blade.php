@extends('layouts.admin')

@section('title', 'Test Header Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-4">Debug Admin Auth</h1>

                <div class="space-y-4">
                    <div>
                        <strong>Auth Status:</strong>
                        @auth('admin')
                            <span class="text-green-600">✅ Authenticated</span>
                        @else
                            <span class="text-red-600">❌ Not Authenticated</span>
                        @endauth
                    </div>

                    @auth('admin')
                        <div>
                            <strong>User:</strong> {{ auth('admin')->user()->name ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Role:</strong> {{ auth('admin')->user()->role ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Guard:</strong> admin
                        </div>
                    @endauth

                    <div>
                        <strong>Current URL:</strong> {{ request()->url() }}
                    </div>

                    <div>
                        <strong>Current Route:</strong> {{ request()->route()->getName() ?? 'N/A' }}
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Vai alla Dashboard
                    </a>
                    @guest('admin')
                        <a href="{{ route('admin.login') }}" class="bg-green-500 text-white px-4 py-2 rounded ml-2">
                            Login Admin
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
