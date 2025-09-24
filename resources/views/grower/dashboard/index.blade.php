<x-grower-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tachometer-alt mr-2"></i>{{ __('Dashboard Coltivatore') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Benvenuto, {{ $grower->name }}!</h3>
                            <p class="text-gray-600">{{ $grower->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 mr-4">
                                <i class="fas fa-seedling text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_products'] }}</p>
                                <p class="text-gray-600">Prodotti Totali</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_products'] }}</p>
                                <p class="text-gray-600">Prodotti Attivi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 mr-4">
                                <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
                                <p class="text-gray-600">Ordini Totali</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_orders'] }}</p>
                                <p class="text-gray-600">Ordini Pendenti</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Quick Actions Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-rocket mr-2"></i>Azioni Rapide
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('grower.products.create') }}"
                               class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                                <i class="fas fa-plus mr-2"></i>Aggiungi Nuovo Prodotto
                            </a>
                            <a href="{{ route('grower.products.index') }}"
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                                <i class="fas fa-seedling mr-2"></i>Gestisci Prodotti
                            </a>
                            <a href="{{ route('grower.orders.index') }}"
                               class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                                <i class="fas fa-shopping-cart mr-2"></i>Visualizza Ordini
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Grower Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Informazioni Coltivatore
                        </h3>
                        <div class="space-y-2">
                            <p><strong>Codice:</strong> {{ $grower->code ?? 'Non assegnato' }}</p>
                            <p><strong>Telefono:</strong> {{ $grower->phone ?? 'Non specificato' }}</p>
                            <p><strong>Città:</strong> {{ $grower->city ?? 'Non specificata' }}</p>
                            <p><strong>Paese:</strong> {{ $grower->country ?? 'Non specificato' }}</p>
                            <p><strong>Stato:</strong>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $grower->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $grower->is_active ? 'Attivo' : 'Inattivo' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            @if($recent_orders->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-history mr-2"></i>Ordini Recenti
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotti</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recent_orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->store->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->products->count() }} prodotto{{ $order->products->count() > 1 ? 'i' : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($order->status)
                                                @case('pending')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('confirmed')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('processing')
                                                    bg-indigo-100 text-indigo-800
                                                    @break
                                                @case('shipped')
                                                    bg-purple-100 text-purple-800
                                                    @break
                                                @case('delivered')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('cancelled')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('grower.orders.show', $order) }}"
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-eye"></i> Visualizza
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('grower.orders.index') }}"
                           class="text-green-600 hover:text-green-900 font-medium">
                            Visualizza tutti gli ordini →
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mx-auto h-12 w-12 flex items-center justify-center bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-shopping-cart text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Nessun ordine ancora</h3>
                    <p class="text-gray-600 mt-2">I tuoi ordini ricevuti appariranno qui quando i garden center effettueranno ordini.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-grower-layout>
