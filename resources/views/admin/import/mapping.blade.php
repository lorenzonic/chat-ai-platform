<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mappatura Colonne CSV - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Mappatura Colonne CSV</h1>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">File caricato</h2>
                <p class="text-gray-600">Righe totali: <span class="font-semibold">{{ $totalRows }}</span></p>
            </div>

            <form method="POST" action="{{ route('admin.import.mapping.process') }}">
                @csrf
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Mappatura Colonne</h2>
                    <p class="text-gray-600 mb-6">Associa ogni colonna del CSV ai campi del database. Seleziona "Ignora" per le colonne non necessarie.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($headers as $index => $header)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Colonna CSV: <span class="font-semibold">{{ $header }}</span>
                                </label>
                                
                                <select name="mapping[{{ $index }}]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">-- Seleziona campo --</option>
                                    @foreach($availableFields as $key => $label)
                                        <option value="{{ $key }}" 
                                            @if(isset($mapping[$key]) && $mapping[$key] == $index) selected @endif>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if(count($preview) > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Anteprima Dati (prime 5 righe)</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50">
                                        @foreach($headers as $header)
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $header }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($preview as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $cell ?? '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="flex justify-between">
                    <a href="{{ route('admin.import.orders') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        ← Torna indietro
                    </a>
                    
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Avvia Import →
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>