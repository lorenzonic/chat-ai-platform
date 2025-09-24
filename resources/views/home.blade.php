<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChatAI Plants - B2B Plant Marketplace</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                🌱 ChatAI Plants
            </h1>
            <p class="text-xl md:text-2xl opacity-90">
                B2B Plant Marketplace con AI Chatbot
            </p>
            <p class="text-lg opacity-75 mt-2">
                La piattaforma completa per garden centers, fornitori e clienti
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Accedi alla Piattaforma
            </h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Scegli il tuo ruolo per accedere alle funzionalità dedicate della piattaforma B2B
            </p>
        </div>

        <!-- Access Cards -->
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">

            <!-- Admin Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-t-4 border-red-500">
                <div class="text-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-3xl">👑</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Super Admin</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Gestione completa della piattaforma, controllo accounts, analytics avanzate e import CSV ordini
                    </p>
                    <ul class="text-sm text-gray-500 mb-8 space-y-2">
                        <li>✅ Gestione multi-tenant</li>
                        <li>✅ Import CSV avanzato</li>
                        <li>✅ Analytics e trends</li>
                        <li>✅ QR code generation</li>
                    </ul>
                    <a href="{{ route('admin.login') }}"
                       class="inline-block w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Accedi come Admin
                    </a>
                </div>
            </div>

            <!-- Store Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-t-4 border-blue-500">
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-3xl">🏪</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Garden Centers</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Gestisci il tuo garden center, ordini, chatbot AI per clienti e sistema di lead generation
                    </p>
                    <ul class="text-sm text-gray-500 mb-8 space-y-2">
                        <li>✅ Chatbot AI personalizzato</li>
                        <li>✅ Gestione ordini</li>
                        <li>✅ Lead generation</li>
                        <li>✅ Analytics clienti</li>
                    </ul>
                    <a href="{{ route('store.login') }}"
                       class="inline-block w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Accedi come Store
                    </a>
                </div>
            </div>

            <!-- Grower Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-t-4 border-green-500">
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-3xl">🌿</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Growers</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Gestisci catalogo prodotti, ordini da garden centers e sistema di stampa etichette bulk
                    </p>
                    <ul class="text-sm text-gray-500 mb-8 space-y-2">
                        <li>✅ Catalogo prodotti</li>
                        <li>✅ Ordini B2B</li>
                        <li>✅ Stampa etichette bulk</li>
                        <li>✅ Analytics vendite</li>
                    </ul>
                    <a href="{{ route('grower.login') }}"
                       class="inline-block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Accedi come Grower
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-20 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-8">Funzionalità della Piattaforma</h3>
            <div class="grid md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-3xl mb-3">🤖</div>
                    <h4 class="font-semibold mb-2">AI Chatbot</h4>
                    <p class="text-sm text-gray-600">NLP italiano con Gemini API</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-3xl mb-3">📊</div>
                    <h4 class="font-semibold mb-2">Import CSV</h4>
                    <p class="text-sm text-gray-600">Sistema avanzato auto-detection</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-3xl mb-3">🏷️</div>
                    <h4 class="font-semibold mb-2">QR & Barcode</h4>
                    <p class="text-sm text-gray-600">Generazione EAN-13 compatibile</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-3xl mb-3">🖨️</div>
                    <h4 class="font-semibold mb-2">Bulk Printing</h4>
                    <p class="text-sm text-gray-600">Stampa etichette retail</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-20">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">
                © {{ date('Y') }} ChatAI Plants - B2B Plant Marketplace Platform
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Powered by Laravel, Vue.js, MySQL & AI
            </p>
        </div>
    </footer>
</body>
</html>
