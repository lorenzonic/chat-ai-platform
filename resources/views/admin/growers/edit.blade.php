@extends('layouts.admin')

@section('title', 'Modifica Coltivatore - ' . $grower->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">✏️ Modifica Coltivatore</h1>
                <p class="mt-2 text-gray-600">Aggiorna le informazioni di <strong>{{ $grower->name }}</strong></p>
            </div>
            <a href="{{ route('admin.growers.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                ← Torna alla Lista
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informazioni Coltivatore</h2>
        </div>

        <form method="POST" action="{{ route('admin.growers.update', $grower) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Coltivatore <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $grower->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $grower->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefono
                    </label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $grower->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Indirizzo
                    </label>
                    <input type="text"
                           id="address"
                           name="address"
                           value="{{ old('address', $grower->address) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tax Code -->
                <div>
                    <label for="tax_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Codice Fiscale
                    </label>
                    <input type="text"
                           id="tax_code"
                           name="tax_code"
                           value="{{ old('tax_code', $grower->tax_code) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tax_code') border-red-500 @enderror"
                           placeholder="RSSMRA80A01H501Z">
                    @error('tax_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- VAT Number -->
                <div>
                    <label for="vat_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Partita IVA
                    </label>
                    <input type="text"
                           id="vat_number"
                           name="vat_number"
                           value="{{ old('vat_number', $grower->vat_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vat_number') border-red-500 @enderror"
                           placeholder="12345678901">
                    @error('vat_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descrizione
                </label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $grower->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Section -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔐 Cambia Password Account</h3>
                <p class="text-sm text-gray-600 mb-4">Lascia vuoto per mantenere la password attuale.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nuova Password
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               autocomplete="new-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Minimo 8 caratteri</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Conferma Nuova Password
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               autocomplete="new-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Current Login Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <h4 class="font-medium text-blue-900 mb-2">🔑 Informazioni Login Attuali</h4>
                <div class="text-sm text-blue-800">
                    <p><strong>Email di accesso:</strong> {{ $grower->email ?: 'Non configurata' }}</p>
                    <p><strong>Account creato:</strong> {{ $grower->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Ultimo aggiornamento:</strong> {{ $grower->updated_at->format('d/m/Y H:i') }}</p>
                    @if($grower->email)
                        <p class="mt-2"><strong>URL di accesso:</strong> <code>{{ url('/grower/login') }}</code></p>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.growers.show', $grower) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    👁️ Visualizza Dettagli
                </a>

                <div class="flex space-x-3">
                    <a href="{{ route('admin.growers.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Annulla
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        💾 Salva Modifiche
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        📦
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Prodotti</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $grower->products()->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        🛒
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Ordini Totali</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $grower->orderItems()->distinct('order_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        💰
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Fatturato</h3>
                    <p class="text-2xl font-bold text-purple-600">€{{ number_format($grower->orderItems()->sum('total_price'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-confirm password matching
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    function checkPasswordMatch() {
        if (password.value && confirmPassword.value) {
            if (password.value === confirmPassword.value) {
                confirmPassword.style.borderColor = '#10B981';
                confirmPassword.style.backgroundColor = '#F0FDF4';
            } else {
                confirmPassword.style.borderColor = '#EF4444';
                confirmPassword.style.backgroundColor = '#FEF2F2';
            }
        } else {
            confirmPassword.style.borderColor = '#D1D5DB';
            confirmPassword.style.backgroundColor = '#FFFFFF';
        }
    }

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
});
</script>
@endsection
