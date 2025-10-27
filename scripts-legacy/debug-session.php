<?php
// Debug delle sessioni di linguaggio

echo "=== DEBUG SESSIONE LINGUAGGIO ===\n";

// Avvia la sessione se non è già avviata
session_start();

echo "Current PHP Session ID: " . session_id() . "\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";

// Test con richieste CURL
echo "\n=== TEST CON CURL ===\n";

// Test 1: Richiesta alla pagina test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/language-test');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Response 1 - HTTP Code: $httpCode1\n";
echo "Response 1 Headers:\n";
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response1, 0, $headerSize);
echo $headers . "\n";

// Test 2: Cambia lingua a inglese
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/language/en');
$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Response 2 (language switch) - HTTP Code: $httpCode2\n";
echo "Response 2 Headers:\n";
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response2, 0, $headerSize);
echo $headers . "\n";

// Test 3: Ritorna alla pagina test per vedere se il linguaggio è cambiato
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/language-test');
curl_setopt($ch, CURLOPT_HEADER, false); // Solo body questa volta
$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Response 3 (after language switch) - HTTP Code: $httpCode3\n";

// Cerca le traduzioni nella response
if (strpos($response3, 'Dashboard') !== false) {
    echo "Found English: Dashboard\n";
}
if (strpos($response3, 'Pannello di controllo') !== false) {
    echo "Found Italian: Pannello di controllo\n";
}
if (strpos($response3, 'Login') !== false) {
    echo "Found English: Login\n";
}
if (strpos($response3, 'Accedi') !== false) {
    echo "Found Italian: Accedi\n";
}

curl_close($ch);

// Cleanup
if (file_exists(__DIR__ . '/cookies.txt')) {
    unlink(__DIR__ . '/cookies.txt');
}

echo "\n=== FINE DEBUG ===\n";
?>
