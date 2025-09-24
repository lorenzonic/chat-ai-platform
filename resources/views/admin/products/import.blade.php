@extends('layouts.admin')

@section('title', 'Importa Prodotti')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Importa Prodotti da CSV/Excel</h1>
                    <a href="{{ route('admin.products.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                        Torna alla Lista
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Import Instructions -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Istruzioni per l'Import</h3>
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>1.</strong> Scarica il template CSV usando il pulsante qui sotto</p>
                        <p><strong>2.</strong> Compila il file con i tuoi ordini di tutti i clienti</p>
                        <p><strong>3.</strong> Il campo <strong>"Codice"</strong> deve contenere il codice cliente per ogni ordine</p>
                        <p><strong>4.</strong> Se il codice cliente non esiste, verrà creato automaticamente un nuovo store (disattivato)</p>
                        <p><strong>5.</strong> Se il fornitore non esiste, verrà creato automaticamente nella tabella fornitori</p>
                        <p><strong>6.</strong> Carica il file per importare tutti gli ordini</p>
                        <p><strong>7.</strong> Il sistema ti mostrerà un riepilogo dell'import</p>
                    </div>
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="text-sm text-yellow-800">
                            <strong>⚠️ Importante:</strong> I nuovi store creati saranno disattivati.
                            L'admin deve attivarli manualmente dalla gestione account.
                        </p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.products.template.download') }}"
                           class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm leading-4 font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Scarica Template CSV
                        </a>
                    </div>
                </div>

                <!-- Duplicate Prevention Notice -->
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">🛡️ Protezione da Duplicati</h3>
                    <div class="text-sm text-green-800 space-y-2">
                        <p><strong>✅ Il campo "CODICE" (codice prodotto) deve essere univoco</strong></p>
                        <p><strong>✅ I prodotti con codice già esistente verranno automaticamente saltati</strong></p>
                        <p><strong>✅ Solo i prodotti nuovi (non esistenti) verranno importati</strong></p>
                        <p><strong>✅ Riceverai un riepilogo dettagliato con il numero di duplicati saltati</strong></p>
                        <p class="text-green-700 font-medium">Questo garantisce che non ci siano mai duplicati nel sistema!</p>
                    </div>
                </div>

                <!-- Import Form -->
                <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700">
                                File CSV/Excel con tutti gli ordini *
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Carica il file degli ordini</span>
                                            <input id="file" name="file" type="file" accept=".csv,.xlsx,.xls" required class="sr-only">
                                        </label>
                                        <p class="pl-1">o trascina qui</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV, XLSX, XLS fino a 20MB</p>
                                    <p class="text-xs text-blue-600">Il sistema assegnerà automaticamente gli ordini ai clienti in base al codice</p>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium">
                                Importa Tutti gli Ordini
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Column Reference -->
                <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Colonne del Template CSV</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                        <div><strong>Fornitore:</strong> Nome del fornitore</div>
                        <div><strong>Prodotto:</strong> Nome del prodotto (obbligatorio)</div>
                        <div><strong>Quantità:</strong> Quantità ordinata</div>
                        <div><strong>CODE:</strong> ⭐ Codice cliente (obbligatorio)</div>
                        <div><strong>CODICE:</strong> Codice prodotto (opzionale)</div>
                        <div><strong>EAN:</strong> Codice EAN/GTIN</div>
                        <div><strong>H:</strong> Altezza in cm</div>
                        <div><strong>Categoria:</strong> Categoria del prodotto</div>
                        <div><strong>Cliente:</strong> Nome cliente</div>
                        <div><strong>CC:</strong> Campo CC</div>
                        <div><strong>PIA:</strong> Campo PIA</div>
                        <div><strong>PRO:</strong> Campo PRO</div>
                        <div><strong>Trasporto:</strong> Costo trasporto</div>
                        <div><strong>Data:</strong> Data consegna (YYYY-MM-DD)</div>
                        <div><strong>Note:</strong> Note aggiuntive</div>
                        <div><strong>€ Vendita:</strong> Prezzo di vendita</div>
                        <div><strong>Indirizzo:</strong> Indirizzo cliente</div>
                        <div><strong>Telefono:</strong> Telefono cliente</div>
                    </div>
                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-sm text-blue-800">
                            <strong>💡 Importante:</strong>
                            <br>• <strong>CODE</strong> = Codice del cliente (obbligatorio) - determina a quale store assegnare l'ordine
                            <br>• <strong>CODICE</strong> = Codice del prodotto (opzionale) - per identificazione interna del prodotto
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// File upload preview
document.getElementById('file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        const fileInfo = document.createElement('p');
        fileInfo.textContent = `File selezionato: ${fileName}`;
        fileInfo.className = 'mt-2 text-sm text-green-600';

        // Remove previous file info
        const existing = e.target.parentNode.parentNode.parentNode.querySelector('.file-info');
        if (existing) existing.remove();

        fileInfo.classList.add('file-info');
        e.target.parentNode.parentNode.parentNode.appendChild(fileInfo);
    }
});
</script>
@endsection
