@extends('layouts.admin')

@section('title', 'Gestione Offerte')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Gestione Offerte</h1>
        <a href="{{ route('admin.offers.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-md font-medium">
            🎯 Nuova Offerta
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.offers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cerca</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Titolo, descrizione, codice..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Tutte</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Attive</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inattive</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Scadute</option>
                </select>
            </div>

            <!-- Grower Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Produttore</label>
                <select name="grower_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Tutti</option>
                    @foreach($growers as $grower)
                        <option value="{{ $grower->id }}" {{ request('grower_id') == $grower->id ? 'selected' : '' }}>
                            {{ $grower->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    🔍 Filtra
                </button>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="mb-4 text-sm text-gray-600">
        Trovate {{ $offers->total() }} offerte
        @if(request()->hasAny(['search', 'status', 'grower_id']))
            <a href="{{ route('admin.offers.index') }}" class="ml-2 text-emerald-600 hover:text-emerald-800">
                (Rimuovi filtri)
            </a>
        @endif
    </div>

    <!-- Offers Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        @if($offers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Offerta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo & Valore
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periodo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Utilizzi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stato
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($offers as $offer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $offer->title }}</div>
                                        @if($offer->code)
                                            <div class="text-sm text-gray-500">Codice: {{ $offer->code }}</div>
                                        @endif
                                        @if($offer->grower)
                                            <div class="text-sm text-blue-600">{{ $offer->grower->name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @switch($offer->type)
                                            @case('percentage')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $offer->discount_value }}% di sconto
                                                </span>
                                                @break
                                            @case('fixed_amount')
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                    €{{ number_format($offer->discount_value, 2) }} di sconto
                                                </span>
                                                @break
                                            @case('buy_x_get_y')
                                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                                                    Compra {{ $offer->buy_quantity }}, prendi {{ $offer->get_quantity }}
                                                </span>
                                                @break
                                        @endswitch
                                    </div>
                                    @if($offer->minimum_amount)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Min: €{{ number_format($offer->minimum_amount, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $offer->start_date->format('d/m/Y H:i') }}</div>
                                    <div class="text-gray-500">{{ $offer->end_date->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $offer->usage_count }}</div>
                                    @if($offer->usage_limit)
                                        <div class="text-gray-500">/ {{ $offer->usage_limit }}</div>
                                    @else
                                        <div class="text-gray-500">/ Illimitato</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($offer->isValid())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Attiva
                                        </span>
                                    @elseif(!$offer->is_active)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Disattivata
                                        </span>
                                    @elseif($offer->end_date->isPast())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Scaduta
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            In programma
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.offers.show', $offer) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            👁️
                                        </a>
                                        <a href="{{ route('admin.offers.edit', $offer) }}"
                                           class="text-green-600 hover:text-green-900">
                                            ✏️
                                        </a>
                                        <form method="POST" action="{{ route('admin.offers.toggle-status', $offer) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-gray-600 hover:text-gray-900"
                                                    title="{{ $offer->is_active ? 'Disattiva' : 'Attiva' }}">
                                                {{ $offer->is_active ? '⏸️' : '▶️' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.offers.destroy', $offer) }}"
                                              class="inline"
                                              onsubmit="return confirm('Sei sicuro di voler eliminare questa offerta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $offers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">🎯</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna offerta trovata</h3>
                <p class="text-gray-500 mb-6">Inizia creando la tua prima offerta promozionale.</p>
                <a href="{{ route('admin.offers.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-md font-medium">
                    🎯 Crea Offerta
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
