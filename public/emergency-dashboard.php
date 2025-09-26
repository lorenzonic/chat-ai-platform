<?php
// Emergency dashboard fallback - minimal Laravel
session_start();

// Check if grower is authenticated
if (!isset($_SESSION['grower_id'])) {
    // Redirect to grower login
    header('Location: /grower/login');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Grower Dashboard - Emergency Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold">Grower Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/grower/products-stickers" class="text-green-600 hover:text-green-700">
                            <i class="fas fa-tags mr-1"></i>Etichette
                        </a>
                        <a href="/grower/logout" class="text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">

                <!-- Alert -->
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                    <div class="flex">
                        <div class="py-1"><i class="fas fa-exclamation-triangle mr-2"></i></div>
                        <div>
                            <p class="font-bold">Modalità di emergenza attiva</p>
                            <p class="text-sm">La dashboard completa è temporaneamente non disponibile. Usa il link "Etichette" per gestire i tuoi prodotti.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-rocket mr-2"></i>Azioni Disponibili
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <a href="/grower/products-stickers"
                               class="block p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-tags text-green-600 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-900">Gestione Etichette</p>
                                        <p class="text-sm text-green-700">Visualizza e stampa etichette prodotti</p>
                                    </div>
                                </div>
                            </a>

                            <a href="/grower/products"
                               class="block p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-seedling text-blue-600 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-900">Gestione Prodotti</p>
                                        <p class="text-sm text-blue-700">Visualizza e modifica i tuoi prodotti</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Informazioni Sistema
                        </h3>
                        <div class="text-sm text-gray-600">
                            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                            <p><strong>Grower ID:</strong> <?php echo htmlspecialchars($_SESSION['grower_id'] ?? 'N/A'); ?></p>
                            <p><strong>Status:</strong> Sistema in modalità di emergenza</p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>
