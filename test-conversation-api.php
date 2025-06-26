<?php
/**
 * Test API per verificare che il chatbot mantenga il contesto della conversazione
 */

// URL base del tuo server Laravel (modifica se necessario)
$baseUrl = 'http://localhost:8000';
$storeSlug = 'garden-center'; // Modifica con il tuo store slug
$sessionId = 'test-conversation-' . time();

echo "🤖 Test Conversazione Chatbot - Verifica Contesto\n";
echo "==============================================\n";
echo "Store: {$storeSlug}\n";
echo "Session ID: {$sessionId}\n\n";

// Funzione per inviare messaggio
function sendMessage($baseUrl, $storeSlug, $sessionId, $message, $userName = null) {
    $url = "{$baseUrl}/api/chatbot/{$storeSlug}/message";

    $data = [
        'message' => $message,
        'session_id' => $sessionId
    ];

    if ($userName) {
        $data['user_name'] = $userName;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data),
            'timeout' => 30
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        echo "❌ Errore nella richiesta\n";
        return null;
    }

    return json_decode($response, true);
}

// Test 1: Primo messaggio con nome
echo "1️⃣ Primo messaggio - Mi presento\n";
echo "Messaggio: \"Il mio nome è Marco\"\n";
$response1 = sendMessage($baseUrl, $storeSlug, $sessionId, "Il mio nome è Marco", "Marco");

if ($response1 && $response1['success']) {
    echo "✅ Risposta AI: " . $response1['response'] . "\n\n";
} else {
    echo "❌ Errore: " . ($response1['error'] ?? 'Risposta non valida') . "\n\n";
    exit(1);
}

sleep(2); // Pausa per simulare conversazione naturale

// Test 2: Secondo messaggio per verificare se ricorda il nome
echo "2️⃣ Secondo messaggio - Verifico se ricorda il nome\n";
echo "Messaggio: \"Come mi chiamo?\"\n";
$response2 = sendMessage($baseUrl, $storeSlug, $sessionId, "Come mi chiamo?", "Marco");

if ($response2 && $response2['success']) {
    echo "✅ Risposta AI: " . $response2['response'] . "\n";

    // Verifico se la risposta contiene il nome
    if (stripos($response2['response'], 'Marco') !== false) {
        echo "🎉 SUCCESSO: L'AI ricorda il nome dalla conversazione precedente!\n\n";
    } else {
        echo "⚠️ ATTENZIONE: L'AI non sembra ricordare il nome dalla conversazione precedente\n\n";
    }
} else {
    echo "❌ Errore: " . ($response2['error'] ?? 'Risposta non valida') . "\n\n";
}

sleep(2);

// Test 3: Terzo messaggio - argomento specifico
echo "3️⃣ Terzo messaggio - Parlo di un problema specifico\n";
echo "Messaggio: \"Ho un pothos con foglie gialle\"\n";
$response3 = sendMessage($baseUrl, $storeSlug, $sessionId, "Ho un pothos con foglie gialle", "Marco");

if ($response3 && $response3['success']) {
    echo "✅ Risposta AI: " . $response3['response'] . "\n\n";
} else {
    echo "❌ Errore: " . ($response3['error'] ?? 'Risposta non valida') . "\n\n";
}

sleep(2);

// Test 4: Quarto messaggio - riferimento al problema precedente
echo "4️⃣ Quarto messaggio - Riferimento al problema precedente\n";
echo "Messaggio: \"Cosa dovrei fare per risolverlo?\"\n";
$response4 = sendMessage($baseUrl, $storeSlug, $sessionId, "Cosa dovrei fare per risolverlo?", "Marco");

if ($response4 && $response4['success']) {
    echo "✅ Risposta AI: " . $response4['response'] . "\n";

    // Verifico se la risposta si riferisce al pothos
    if (stripos($response4['response'], 'pothos') !== false ||
        stripos($response4['response'], 'foglie gialle') !== false ||
        stripos($response4['response'], 'innaffiare') !== false ||
        stripos($response4['response'], 'acqua') !== false) {
        echo "🎉 SUCCESSO: L'AI ricorda il problema del pothos dalla conversazione!\n\n";
    } else {
        echo "⚠️ ATTENZIONE: L'AI non sembra collegare la risposta al problema precedente\n\n";
    }
} else {
    echo "❌ Errore: " . ($response4['error'] ?? 'Risposta non valida') . "\n\n";
}

echo "🏁 Test completato!\n";
echo "=================\n";
echo "Verifica i risultati sopra per vedere se il chatbot mantiene il contesto della conversazione.\n";
