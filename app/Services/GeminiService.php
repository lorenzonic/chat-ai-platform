<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Store;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = config('services.gemini.api_url');
    }

    /**
     * Generate AI response using Gemini API with knowledge base check
     *
     * @param string $message User message
     * @param array $context Store context information
     * @param Store|null $store Store instance for knowledge search
     * @return string AI response
     * @throws Exception
     */
    public function generateResponse(string $message, array $context = [], Store $store = null): string
    {
        // Prima cerca nella knowledge base dello store
        if ($store) {
            $knowledgeService = new KnowledgeSearchService();
            $knowledgeItem = $knowledgeService->searchKnowledge($store, $message);

            if ($knowledgeItem) {
                // Se trova una risposta nella knowledge base, usala
                Log::info('Knowledge base match found', [
                    'store_id' => $store->id,
                    'question' => $knowledgeItem->question,
                    'user_query' => $message
                ]);

                return $knowledgeItem->answer;
            }
        }

        // Se non trova nulla nella knowledge base, usa l'AI normale
        return $this->generateAIResponse($message, $context);
    }

    /**
     * Generate AI response using Gemini API (internal method)
     *
     * @param string $message User message
     * @param array $context Store context information
     * @return string AI response
     * @throws Exception
     */
    private function generateAIResponse(string $message, array $context = []): string
    {
        try {
            // Prepara il contesto per l'AI
            $systemPrompt = $this->buildSystemPrompt($context);
            $fullPrompt = $systemPrompt . "\n\nDomanda del cliente: " . $message;

            // Prepare the request payload
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $fullPrompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];

            // Make the API request
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '?key=' . $this->apiKey, $payload);

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('API request failed: ' . $response->status());
            }

            $data = $response->json();

            // Extract the response text
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($data['candidates'][0]['content']['parts'][0]['text']);
            }

            throw new Exception('Invalid response format from Gemini API');

        } catch (Exception $e) {
            Log::error('Gemini service error', [
                'message' => $e->getMessage(),
                'context' => $context
            ]);

            // Fallback response
            return $this->getFallbackResponse();
        }
    }

    /**
     * Generate AI response using Gemini API with knowledge base check
     *
     * @param string $message User message
     * @param array $context Store context information
     * @param Store|null $store Store instance for knowledge search
     * @return string AI response
     * @throws Exception
     */
    public function generateResponseWithKnowledge(string $message, array $context = [], Store $store = null): string
    {
        // Prima cerca nella knowledge base dello store
        if ($store) {
            $knowledgeService = new KnowledgeSearchService();
            $knowledgeItem = $knowledgeService->searchKnowledge($store, $message);

            if ($knowledgeItem) {
                // Se trova una risposta nella knowledge base, usala
                Log::info('Knowledge base match found', [
                    'store_id' => $store->id,
                    'question' => $knowledgeItem->question,
                    'user_query' => $message
                ]);

                return $knowledgeItem->answer;
            }
        }

        // Se non trova nulla nella knowledge base, usa l'AI normale
        return $this->generateResponse($message, $context, $store);
    }

    /**
     * Build system prompt with store context and conversation history
     *
     * @param array $context
     * @return string
     */
    private function buildSystemPrompt(array $context): string
    {
        $storeName = $context['store_name'] ?? 'il nostro negozio';
        $storeDescription = $context['store_description'] ?? 'un negozio specializzato';
        $assistantName = $context['assistant_name'] ?? 'Assistente';
        $chatContext = $context['chat_context'] ?? '';
        $openingHours = $context['opening_hours'] ?? null;
        $aiTone = $context['chat_ai_tone'] ?? 'professional';
        $userName = $context['user_name'] ?? null;
        $isFirstMessage = $context['is_first_message'] ?? false;
        $conversationHistory = $context['conversation_history'] ?? collect();

        // === NUOVO: DATI NLP ===
        $nlpAnalysis = $context['nlp_analysis'] ?? null;
        $detectedIntent = $context['detected_intent'] ?? null;
        $keywords = $context['keywords'] ?? [];
        $entities = $context['entities'] ?? [];

        // Informazioni del profilo
        $phone = $context['phone'] ?? null;
        $address = $context['address'] ?? null;
        $city = $context['city'] ?? null;
        $state = $context['state'] ?? null;
        $postalCode = $context['postal_code'] ?? null;
        $country = $context['country'] ?? null;
        $website = $context['website'] ?? null;

        $prompt = "Sei {$assistantName}, un assistente AI per {$storeName}";

        if ($storeDescription) {
            $prompt .= ", {$storeDescription}";
        }

        $prompt .= ".";

        // Add personalization if user name is available
        if ($userName) {
            $prompt .= "\n\nIl cliente si chiama {$userName}. Usa il suo nome quando appropriato per personalizzare la conversazione.";
        }

        // Add conversation history if available
        if ($conversationHistory->isNotEmpty()) {
            $prompt .= "\n\nCRONOLOGIA CONVERSAZIONE:";
            foreach ($conversationHistory as $chat) {
                $prompt .= "\nCliente: " . $chat->user_message;
                $prompt .= "\nTu: " . $chat->ai_response;
            }
            $prompt .= "\n\nTieni conto di questa cronologia per fornire risposte coerenti e non ripetere informazioni già fornite.";
        }

        // === NUOVO: INFORMAZIONI ANALISI NLP ===
        if ($nlpAnalysis) {
            $prompt .= "\n\nANALISI DEL MESSAGGIO UTENTE:";

            if ($detectedIntent && $detectedIntent !== 'altro') {
                $prompt .= "\n• Intent rilevato: {$detectedIntent}";

                // Aggiungi istruzioni specifiche per intent
                switch ($detectedIntent) {
                    case 'cura':
                        $prompt .= "\n• Il cliente ha una domanda sulla CURA delle piante. Fornisci una risposta dettagliata e pratica.";
                        break;
                    case 'acquisto':
                        $prompt .= "\n• Il cliente è interessato all'ACQUISTO. Fornisci informazioni su disponibilità, prezzi indicativi o dove trovare il prodotto.";
                        break;
                    case 'identificazione':
                        $prompt .= "\n• Il cliente vuole IDENTIFICARE una pianta. Aiutalo con domande specifiche o suggerisci di caricare una foto.";
                        break;
                    case 'consiglio':
                        $prompt .= "\n• Il cliente cerca CONSIGLI. Chiedi dettagli sulle sue esigenze per dare suggerimenti personalizzati.";
                        break;
                }
            }

            if (!empty($keywords)) {
                $prompt .= "\n• Parole chiave identificate: " . implode(', ', array_slice($keywords, 0, 5));
            }

            if (!empty($entities)) {
                $entitiesText = [];
                foreach ($entities as $entity) {
                    $entitiesText[] = $entity['text'] . ' (' . $entity['label'] . ')';
                }
                $prompt .= "\n• Entità riconosciute: " . implode(', ', array_slice($entitiesText, 0, 3));
            }

            $prompt .= "\n\nUsa queste informazioni per fornire una risposta più mirata e pertinente.";
        }

        // Add specific instructions for continuing conversations
        if (!$isFirstMessage) {
            $prompt .= "\n\nQuesta è una conversazione in corso. NON salutare e NON ripetere presentazioni. Mantieni la coerenza con quanto già discusso.";
        }

        $prompt .= "

TONO DI VOCE: " . $this->getToneInstructions($aiTone) . "

ISTRUZIONI IMPORTANTI:
- Rispondi sempre in italiano
- NON salutare mai all'inizio delle tue risposte (non usare Ciao, Salve, Buongiorno ecc.)
- Vai direttamente al punto senza saluti introduttivi
- MANTIENI LA COERENZA con la conversazione precedente
- NON ripetere informazioni già fornite nella cronologia
- Se il cliente fa riferimento a qualcosa già discusso, dimostra di ricordarlo
- Usa emoji appropriate per rendere le risposte più coinvolgenti 🌿✨
- Per domande sulla cura delle piante, sii più dettagliato e specifico
- Per altre domande, mantieni le risposte brevi (massimo 2-3 frasi)
- Usa formattazione del testo per migliorare la leggibilità:
  * **grassetto** per concetti importanti
  * *corsivo* per enfatizzare
  * • punti elenco per liste
  * 🌱 emoji per argomenti relativi alle piante
- Concentrati sulla domanda specifica del cliente
- Se non conosci la risposta, ammettilo onestamente
- Non inventare informazioni su prezzi o disponibilità prodotti
- Suggerisci di visitare il negozio SOLO se la domanda richiede una consulenza personalizzata o una valutazione fisica
- Puoi fornire informazioni di contatto e posizione se richieste
- EVITA frasi come 'Come posso aiutarti', 'Cosa posso fare per te', 'Posso aiutarti con altro'
- EVITA frasi di cortesia generiche e vai diretto al contenuto
- Non ripetere informazioni già condivise nella conversazione";

        if ($userName) {
            $prompt .= "
- Usa il nome {$userName} occasionalmente per personalizzare le risposte, ma non ad ogni messaggio";
        }

        $prompt .= "

ESEMPI DI COSA NON FARE:
❌ Ciao! Come posso aiutarti oggi?
❌ Buongiorno! Sono qui per aiutarti!
❌ Posso fare altro per te?

ESEMPI DI COSA FARE:
✅ Risposta diretta alla domanda
✅ Informazioni utili e specifiche
✅ Consigli pratici immediati

REGOLE SPECIALI PER DOMANDE SULLE PIANTE:
- Se la domanda riguarda cura, malattie, irrigazione, luce, concimazione, potatura o problemi delle piante, fornisci risposte dettagliate
- Includi consigli pratici e specifici
- Usa emoji appropriate: 🌿🌱💧☀️🌸🍃✂️🪴
- Organizza le informazioni in punti chiari
- Fornisci tempistiche e frequenze quando rilevanti

ESEMPIO DI RISPOSTA DETTAGLIATA PER PIANTE:
Domanda: \"Come curare un pothos?\"
Risposta: \"Il **pothos** 🌿 è perfetto per principianti! Ecco come curarlo:

• **Luce**: Luce indiretta brillante ☀️ (evita sole diretto)
• **Irrigazione**: Quando il terreno è asciutto in superficie 💧 (ogni 7-10 giorni)
• **Terreno**: Ben drenante, puoi usare terriccio universale 🪴
• **Temperatura**: 18-24°C ideale 🌡️

**Consigli extra**: Pulisci le foglie mensilmente e pota i rami troppo lunghi ✂️\"

CONTESTO DEL NEGOZIO:
Nome: {$storeName}";

        if ($storeDescription) {
            $prompt .= "
Descrizione: {$storeDescription}";
        }

        // Aggiungi informazioni di contatto se disponibili
        if ($phone || $address || $city || $website) {
            $prompt .= "

INFORMAZIONI DI CONTATTO:";

            if ($phone) {
                $prompt .= "
Telefono: {$phone}";
            }

            if ($address || $city || $state || $postalCode || $country) {
                $prompt .= "
Indirizzo: ";
                $addressParts = array_filter([$address, $city, $state, $postalCode, $country]);
                $prompt .= implode(', ', $addressParts);
            }

            if ($website) {
                $prompt .= "
Sito web: {$website}";
            }
        }

        if ($openingHours) {
            $hours = is_array($openingHours) ? json_encode($openingHours) : $openingHours;
            $prompt .= "
Orari di apertura: {$hours}";
        }

        if ($chatContext) {
            $prompt .= "

INFORMAZIONI SPECIFICHE DEL NEGOZIO:
{$chatContext}";
        }

        return $prompt;
    }

    /**
     * Get fallback response when AI fails
     *
     * @return string
     */
    private function getFallbackResponse(): string
    {
        return "Mi dispiace, in questo momento sto avendo difficoltà tecniche. Riprova tra qualche istante o contattaci direttamente per assistenza.";
    }

    /**
     * Get tone instructions based on selected AI tone
     *
     * @param string $tone
     * @return string
     */
    private function getToneInstructions(string $tone): string
    {
        return match($tone) {
            'friendly' => 'Sii molto amichevole, caloroso e accogliente. Usa un linguaggio informale ma rispettoso. Dimostra genuino interesse per il cliente. Usa emoji sorridenti e positive quando appropriate 😊🌟.',
            'cheerful' => 'Sii allegro, positivo ed entusiasta. Usa esclamazioni moderate e un linguaggio vivace. Trasmetti energia positiva. Usa emoji gioiose e colorate 🎉🌈✨.',
            'green_passion' => 'Sii appassionato di ambiente e sostenibilità. Enfatizza aspetti ecologici quando pertinenti. Usa terminologia "verde" e dimostra cura per la natura. Usa emoji naturali 🌿🌍💚🌱.',
            'professional' => 'Mantieni un tono professionale, cortese e competente. Sii formale ma accessibile. Usa emoji professionali con moderazione ✅📋.',
            default => 'Sii cordiale, professionale e utile.'
        };
    }

    /**
     * Check if Gemini service is configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiUrl);
    }
}
