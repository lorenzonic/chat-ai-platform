@extends('layouts.admin')

@section('title', 'Ordine: ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📋 Ordine {{ $order->order_number }}</h1>
                <p class="mt-2 text-gray-600">Dettagli completi dell'ordine</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.orders.edit', $order) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    ✏️ Modifica
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    ← Torna agli Ordini
                </a>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Informazioni Ordine</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Numero Ordine</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->order_number }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Store</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="font-medium">{{ $order->store->name }}</div>
                            <div class="text-gray-500">{{ $order->store->email }}</div>
                        </dd>
                    </div>

                    @if($order->client)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->client }}</dd>
                    </div>
                    @endif

                    @if($order->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telefono</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->phone }}</dd>
                    </div>
                    @endif

                    @if($order->cc)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">CC</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->cc }}</dd>
                    </div>
                    @endif

                    @if($order->pia)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">PIA</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->pia }}</dd>
                    </div>
                    @endif

                    @if($order->pro)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">PRO</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->pro }}</dd>
                    </div>
                    @endif

                    @if($order->transport)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Trasporto</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->transport }}</dd>
                    </div>
                    @endif

                    @if($order->transport_cost)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Costo Trasporto</dt>
                        <dd class="mt-1 text-sm text-gray-900">€{{ number_format((float) $order->transport_cost, 2, ',', '.') }}</dd>
                    </div>
                    @endif

                    @if($order->delivery_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data Consegna</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ date('d/m/Y', strtotime($order->delivery_date)) }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data Creazione</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </div>

                @if($order->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Note</dt>
                    <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $order->notes }}</dd>
                </div>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiche</h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Items Totali</span>
                        <span class="text-sm font-medium text-gray-900">{{ $order->orderItems->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Growers Coinvolti</span>
                        <span class="text-sm font-medium text-gray-900">{{ $order->orderItems->unique('grower_id')->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Valore Totale Items</span>
                        <span class="text-sm font-medium text-gray-900">
                            €{{ number_format($order->orderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 2, ',', '.') }}
                        </span>
                    </div>

                    @if($order->transport_cost)
                    <div class="flex items-center justify-between border-t pt-2">
                        <span class="text-sm text-gray-500">+ Trasporto</span>
                        <span class="text-sm font-medium text-gray-900">€{{ number_format((float) $order->transport_cost, 2, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between font-medium">
                        <span class="text-sm text-gray-900">Totale Ordine</span>
                        <span class="text-sm text-gray-900">
                            €{{ number_format($order->orderItems->sum(function($item) { return $item->quantity * $item->unit_price; }) + (float) $order->transport_cost, 2, ',', '.') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    @if($order->orderItems->count() > 0)
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Items Ordine</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grower</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantità</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo Unit.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Totale</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->orderItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $item->product_info['name'] ?? 'Prodotto rimosso' }}
                            </div>
                            @if(isset($item->product_info['code']))
                            <div class="text-sm text-gray-500 font-mono">{{ $item->product_info['code'] }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $item->grower->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">€{{ number_format($item->unit_price, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">€{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white shadow-sm rounded-lg">
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📦</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun item nell'ordine</h3>
            <p class="text-gray-600 mb-4">Questo ordine non contiene ancora prodotti.</p>
            <a href="{{ route('admin.import.orders') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                📥 Importa Prodotti
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
