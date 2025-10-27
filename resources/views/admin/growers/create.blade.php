@extends('layouts.admin')

@section('title', 'Nuovo Coltivatore')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">➕ Nuovo Coltivatore</h1>
                <p class="mt-2 text-gray-600">Aggiungi un nuovo coltivatore/fornitore al sistema</p>
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

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informazioni Coltivatore</h2>
        </div>

        <form method="POST" action="{{ route('admin.growers.store') }}" class="p-6 space-y-6">
            @csrf

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
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email (per accesso account)
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Se fornita, il coltivatore potrà accedere al sistema</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefono
                    </label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone') }}"
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
                           value="{{ old('address') }}"
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
                           value="{{ old('tax_code') }}"
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
                           value="{{ old('vat_number') }}"
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
                          placeholder="Descrizione dell'attività del coltivatore..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Section (only if email provided) -->
            <div class="border-t border-gray-200 pt-6" id="password-section" style="display: none;">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔐 Password Account</h3>
                <p class="text-sm text-gray-600 mb-4">Se l'email è fornita, imposta una password per l'accesso.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               autocomplete="new-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Minimo 8 caratteri. Lascia vuoto per usare password di default: <code>password123</code></p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Conferma Password
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               autocomplete="new-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <h4 class="font-medium text-blue-900 mb-2">ℹ️ Informazioni</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Il <strong>nome</strong> è obbligatorio e identificherà il coltivatore nel sistema</li>
                    <li>• Se fornisci un'<strong>email</strong>, il coltivatore potrà accedere al sistema con le proprie credenziali</li>
                    <li>• Se non imposti una password, verrà usata quella di default: <code>password123</code></li>
                    <li>• Il coltivatore potrà accedere tramite: <code>{{ url('/grower/login') }}</code></li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-200 space-x-3">
                <a href="{{ route('admin.growers.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Annulla
                </a>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    ➕ Crea Coltivatore
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordSection = document.getElementById('password-section');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    // Show/hide password section based on email input
    function togglePasswordSection() {
        if (emailInput.value.trim()) {
            passwordSection.style.display = 'block';
        } else {
            passwordSection.style.display = 'none';
            passwordInput.value = '';
            confirmPasswordInput.value = '';
        }
    }

    // Check password matching
    function checkPasswordMatch() {
        if (passwordInput.value && confirmPasswordInput.value) {
            if (passwordInput.value === confirmPasswordInput.value) {
                confirmPasswordInput.style.borderColor = '#10B981';
                confirmPasswordInput.style.backgroundColor = '#F0FDF4';
            } else {
                confirmPasswordInput.style.borderColor = '#EF4444';
                confirmPasswordInput.style.backgroundColor = '#FEF2F2';
            }
        } else {
            confirmPasswordInput.style.borderColor = '#D1D5DB';
            confirmPasswordInput.style.backgroundColor = '#FFFFFF';
        }
    }

    emailInput.addEventListener('input', togglePasswordSection);
    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Initial check
    togglePasswordSection();
});
</script>
@endsection
