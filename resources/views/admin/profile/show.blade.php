@extends('layouts.admin')

@section('title', 'Il mio profilo')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">👤 Il mio profilo</h1>
                <p class="mt-2 text-gray-600">Gestisci le tue informazioni personali e impostazioni</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.profile.edit') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    ✏️ Modifica profilo
                </a>
                <a href="{{ route('admin.profile.password.edit') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    🔒 Cambia password
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informazioni profilo</h2>
        </div>

        <div class="px-6 py-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nome</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $admin->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $admin->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ruolo</dt>
                    <dd class="mt-1 text-sm text-gray-900">Amministratore</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Data registrazione</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $admin->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ultimo accesso</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $admin->updated_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Attività recente</h2>
        </div>

        <div class="px-6 py-6">
            <div class="text-center py-8">
                <div class="text-gray-400 text-4xl mb-4">📊</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Attività di amministrazione</h3>
                <p class="text-gray-600">Le tue attività recenti verranno visualizzate qui.</p>
            </div>
        </div>
    </div>
</div>
@endsection