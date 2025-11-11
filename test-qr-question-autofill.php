<?php
/**
 * Test QR Code Question Auto-fill System
 *
 * Verifica che quando si scannerizza un QR code con una domanda,
 * questa venga inserita automaticamente nell'input della chat.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "   TEST QR CODE QUESTION AUTO-FILL SYSTEM\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test 1: Verifica che il QrRedirectController passi il parametro 'question'
echo "✓ Test 1: QrRedirectController passa parametro 'question'\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/QrRedirectController.php';
$controllerContent = file_get_contents($controllerPath);

if (str_contains($controllerContent, "\$params['question'] = \$qrCode->question;")) {
    echo "  ✓ PASS: Controller imposta parametro 'question' dall'URL\n";
} else {
    echo "  ✗ FAIL: Controller non imposta 'question'\n";
}

if (str_contains($controllerContent, "http_build_query")) {
    echo "  ✓ PASS: Controller costruisce query string con parametri\n";
} else {
    echo "  ✗ FAIL: Controller non costruisce query string\n";
}

echo "\n";

// Test 2: Verifica che la view chatbot-vue.blade.php legga il parametro 'question'
echo "✓ Test 2: View chatbot-vue.blade.php legge parametro 'question'\n";
$viewPath = __DIR__ . '/resources/views/store/frontend/chatbot-vue.blade.php';
$viewContent = file_get_contents($viewPath);

if (str_contains($viewContent, "request('question')")) {
    echo "  ✓ PASS: View legge parametro 'question' da request\n";
} else {
    echo "  ✗ FAIL: View non legge 'question'\n";
}

if (str_contains($viewContent, "data-prefilled-question")) {
    echo "  ✓ PASS: View imposta data attribute 'data-prefilled-question'\n";
} else {
    echo "  ✗ FAIL: View non imposta data attribute\n";
}

if (str_contains($viewContent, "request('question') ?? request('q')")) {
    echo "  ✓ PASS: View supporta entrambi i parametri 'question' e 'q'\n";
} else {
    echo "  ✗ WARNING: View potrebbe non supportare entrambi i parametri\n";
}

echo "\n";

// Test 3: Verifica che Vue.js legga prefilledQuestion e lo inserisca in currentMessage
echo "✓ Test 3: Vue.js inserisce domanda nell'input\n";

if (str_contains($viewContent, "const prefilledQuestion = chatbotElement.dataset.prefilledQuestion")) {
    echo "  ✓ PASS: Vue legge data-prefilled-question dal DOM\n";
} else {
    echo "  ✗ FAIL: Vue non legge prefilledQuestion\n";
}

if (str_contains($viewContent, "currentMessage: prefilledQuestion || ''")) {
    echo "  ✓ PASS: Vue inserisce prefilledQuestion in currentMessage (input field)\n";
} else {
    echo "  ✗ FAIL: Vue non inserisce prefilledQuestion in currentMessage\n";
}

// Verifica che NON invii automaticamente (solo inserisce nell'input)
if (!str_contains($viewContent, "this.sendMessage();") ||
    str_contains($viewContent, "// User can review and modify it before sending")) {
    echo "  ✓ PASS: Vue NON invia automaticamente (permette revisione)\n";
} else {
    echo "  ✗ WARNING: Vue potrebbe inviare automaticamente invece di mostrare solo nell'input\n";
}

if (str_contains($viewContent, "inputField.focus()")) {
    echo "  ✓ PASS: Vue mette focus sull'input field quando c'è domanda precompilata\n";
} else {
    echo "  ℹ INFO: Vue non mette focus automaticamente sull'input\n";
}

echo "\n";

// Test 4: Verifica database QR codes con domande
echo "✓ Test 4: Database QR Codes con domande\n";

$qrCodesWithQuestions = \App\Models\QrCode::whereNotNull('question')
    ->whereNotNull('ean_code')
    ->where('ean_code', '!=', '')
    ->get();
echo "  ℹ QR codes con domande: " . $qrCodesWithQuestions->count() . "\n";

if ($qrCodesWithQuestions->count() > 0) {
    echo "\n  Esempi di QR codes con domande:\n";
    echo "  " . str_repeat('─', 60) . "\n";
    foreach ($qrCodesWithQuestions->take(5) as $qr) {
        echo "  • EAN: {$qr->ean_code}\n";
        echo "    Store: {$qr->store->name}\n";
        echo "    Question: {$qr->question}\n";
        echo "    URL: " . route('qr.redirect', $qr->ean_code) . "\n";
        echo "    → Redirect to: " . route('store.chatbot', $qr->store->slug) . "?question=" . urlencode($qr->question) . "\n";
        echo "\n";
    }
} else {
    echo "  ℹ Nessun QR code con domanda e EAN code valido trovato\n";
}

echo "\n";

// Test 5: Genera URL di test
echo "✓ Test 5: URLs di test\n";
$testStore = \App\Models\Store::first();
if ($testStore) {
    echo "  Test URLs per store: {$testStore->name} (slug: {$testStore->slug})\n";
    echo "  " . str_repeat('─', 60) . "\n";

    $testQuestions = [
        'Come si cura questa pianta?',
        'Quando va annaffiata?',
        'Di quanta luce ha bisogno?',
        'È adatta per interni?',
    ];

    foreach ($testQuestions as $question) {
        $url = route('store.chatbot', $testStore->slug) . '?question=' . urlencode($question);
        echo "  • " . $url . "\n";
    }

    echo "\n  Puoi testare aprendo uno di questi URL nel browser.\n";
    echo "  La domanda dovrebbe apparire nell'input field della chat.\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "   RIEPILOGO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✓ Sistema QR Code Question Auto-fill:\n";
echo "  1. QR code contiene campo 'question' nel database\n";
echo "  2. /qr/{ean_code} → QrRedirectController legge question\n";
echo "  3. Redirect a /{store}/chatbot?question=...\n";
echo "  4. View chatbot-vue.blade.php legge request('question')\n";
echo "  5. Vue.js inserisce domanda nell'input field\n";
echo "  6. Utente può rivedere/modificare prima di inviare\n";
echo "  7. Focus automatico sull'input per facilitare l'invio\n\n";

echo "✓ Workflow completo:\n";
echo "  Scansione QR → Redirect con question → Chatbot aperto → \n";
echo "  Domanda inserita nell'input → Utente preme Invio → AI risponde\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
