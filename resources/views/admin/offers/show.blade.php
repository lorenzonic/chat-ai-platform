@extends('layouts.admin')

@section('title', 'Dettagli Offerta')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $offer->title }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.offers.edit', $offer) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md">
                ✏️ Modifica
            </a>
            <form method="POST" action="{{ route('admin.offers.toggle-status', $offer) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    {{ $offer->is_active ? '⏸️ Disattiva' : '▶️ Attiva' }}
                </button>
            </form>
            <a href="{{ route('admin.offers.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                ← Torna alle Offerte
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informazioni Generali</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Titolo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $offer->title }}</dd>
                    </div>

                    @if($offer->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Descrizione</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $offer->description }}</dd>
                    </div>
                    @endif

                    @if($offer->code)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Codice Promozionale</dt>
                        <dd class="mt-1">
                            <code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">{{ $offer->code }}</code>
                        </dd>
                    </div>
                    @endif

                    @if($offer->grower)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Produttore</dt>
                        <dd class="mt-1 text-sm text-blue-600">{{ $offer->grower->name }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stato</dt>
                        <dd class="mt-1">
                            @if($offer->isValid())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    🟢 Attiva
                                </span>
                            @elseif(!$offer->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    ⏸️ Disattivata
                                </span>
                            @elseif($offer->end_date->isPast())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    🔴 Scaduta
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    🟡 In programma
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Creata il</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $offer->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </div>
            </div>

            <!-- Offer Details -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Dettagli Offerta</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo di Sconto</dt>
                        <dd class="mt-1">
                            @switch($offer->type)
                                @case('percentage')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                        📊 Percentuale: {{ $offer->discount_value }}%
                                    </span>
                                    @break
                                @case('fixed_amount')
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                        💰 Importo Fisso: €{{ number_format($offer->discount_value, 2) }}
                                    </span>
                                    @break
                                @case('buy_x_get_y')
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                        🎁 Compra {{ $offer->buy_quantity }}, Prendi {{ $offer->get_quantity }}
                                    </span>
                                    @break
                            @endswitch
                        </dd>
                    </div>

                    @if($offer->minimum_amount)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Importo Minimo</dt>
                        <dd class="mt-1 text-sm text-gray-900">€{{ number_format($offer->minimum_amount, 2) }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Periodo di Validità</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            Dal {{ $offer->start_date->format('d/m/Y H:i') }}<br>
                            Al {{ $offer->end_date->format('d/m/Y H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Utilizzi</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $offer->usage_count }}
                            @if($offer->usage_limit)
                                / {{ $offer->usage_limit }} ({{ round(($offer->usage_count / $offer->usage_limit) * 100) }}%)
                            @else
                                / Illimitato
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Product/Category Restrictions -->
            @if($offer->applicable_products || $offer->applicable_categories)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Restrizioni</h2>

                @if($offer->applicable_products && count($offer->applicable_products) > 0)
                <div class="mb-4">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Prodotti Specifici</dt>
                    <dd class="flex flex-wrap gap-2">
                        @php
                            $products = \App\Models\Product::whereIn('id', $offer->applicable_products)->with('grower')->get();
                        @endphp
                        @foreach($products as $product)
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                {{ $product->name }} ({{ $product->grower->name ?? 'N/A' }})
                            </span>
                        @endforeach
                    </dd>
                </div>
                @endif

                @if($offer->applicable_categories && count($offer->applicable_categories) > 0)
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-2">Categorie Specifiche</dt>
                    <dd class="flex flex-wrap gap-2">
                        @foreach($offer->applicable_categories as $category)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                {{ $category }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                @endif
            </div>
            @endif

            <!-- Order History -->
            @if($offer->orders->count() > 0)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ordini che hanno utilizzato questa offerta</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordine</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sconto Applicato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($offer->orders->take(10) as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->client }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    €{{ number_format($order->pivot->discount_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->pivot->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($offer->orders->count() > 10)
                    <p class="text-sm text-gray-500 mt-2">
                        Mostrando i primi 10 ordini di {{ $offer->orders->count() }} totali.
                    </p>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar Stats -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiche</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Utilizzi Totali</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $offer->usage_count }}</span>
                    </div>

                    @if($offer->usage_limit)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Limite Utilizzi</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $offer->usage_limit }}</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full"
                             style="width: {{ min(100, ($offer->usage_count / $offer->usage_limit) * 100) }}%"></div>
                    </div>
                    @endif

                    @php
                        $totalDiscount = $offer->orders->sum('pivot.discount_amount');
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Sconto Totale Erogato</span>
                        <span class="text-lg font-semibold text-emerald-600">€{{ number_format($totalDiscount, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Ordini Coinvolti</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $offer->orders->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Time Remaining -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tempo Rimanente</h3>
                @if($offer->end_date->isFuture())
                    @php
                        $daysRemaining = now()->diffInDays($offer->end_date);
                        $hoursRemaining = now()->diffInHours($offer->end_date) % 24;
                    @endphp
                    <div class="text-center">
                        <div class="text-3xl font-bold text-emerald-600">{{ $daysRemaining }}</div>
                        <div class="text-sm text-gray-500">giorni rimanenti</div>
                        @if($daysRemaining == 0)
                            <div class="text-lg font-medium text-orange-600 mt-2">{{ $hoursRemaining }}h rimanenti</div>
                        @endif
                    </div>
                @else
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600">SCADUTA</div>
                        <div class="text-sm text-gray-500">
                            {{ $offer->end_date->diffForHumans() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Azioni Rapide</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.offers.toggle-status', $offer) }}" class="w-full">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full {{ $offer->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-md text-sm">
                            {{ $offer->is_active ? '⏸️ Disattiva Offerta' : '▶️ Attiva Offerta' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.offers.edit', $offer) }}"
                       class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm text-center block">
                        ✏️ Modifica Offerta
                    </a>

                    <form method="POST" action="{{ route('admin.offers.destroy', $offer) }}"
                          class="w-full"
                          onsubmit="return confirm('Sei sicuro di voler eliminare questa offerta? Questa azione non può essere annullata.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                            🗑️ Elimina Offerta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
