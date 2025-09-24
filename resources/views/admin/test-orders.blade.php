@extends('layouts.admin')

@section('title', 'Test Ordini')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Test Pagina Ordini</h1>
                <p class="text-gray-600">Se vedi questo messaggio, il sistema di routing e viste funziona correttamente.</p>

                <div class="mt-6">
                    <a href="{{ route('admin.orders.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Vai alla Pagina Ordini Reale
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
