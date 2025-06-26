<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\QrCode;
use App\Models\ChatLog;
use App\Models\QrScan;
use App\Models\Interaction;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(
        private GeminiService $geminiService
    ) {}

    /**
     * Send message to chatbot and get AI response
     *
     * @param Request $request
     * @param Store $store
     * @return JsonResponse
     */
    public function sendMessage(Request $request, Store $store): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string',
            'ref' => 'nullable|string', // QR code reference
            'user_name' => 'nullable|string|max:100', // User's name for personalization
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_data' => 'nullable|array', // Additional location info
        ]);

        $message = trim($request->input('message'));
        $sessionId = $request->input('session_id', Str::uuid());
        $refCode = $request->input('ref');
        $userName = $request->input('user_name');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $locationData = $request->input('location_data', []);

        // Find QR code if reference provided
        $qrCode = null;
        if ($refCode) {
            $qrCode = QrCode::where('ref_code', $refCode)
                ->where('store_id', $store->id)
                ->first();
        }

        try {
            // Check if chat is enabled
            if (!$store->chat_enabled) {
                return response()->json([
                    'success' => false,
                    'error' => 'Il servizio chat è temporaneamente non disponibile.',
                    'session_id' => $sessionId,
                ], 503);
            }

            // Check if this is a continuing conversation and get conversation history
            $conversationHistory = ChatLog::where('session_id', $sessionId)
                ->where('store_id', $store->id)
                ->orderBy('created_at', 'asc')
                ->take(10) // Limit to last 10 exchanges to avoid token limit
                ->get();

            $isFirstMessage = $conversationHistory->isEmpty();

            // Prepare context for AI with store's custom settings and conversation history
            $context = [
                'store_name' => $store->name,
                'store_description' => $store->description,
                'is_premium' => $store->is_premium,
                'assistant_name' => $store->assistant_name ?? 'Assistente',
                'chat_context' => $store->chat_context,
                'opening_hours' => $store->opening_hours,
                'chat_ai_tone' => $store->chat_ai_tone ?? 'professional',
                'user_name' => $userName, // Add user name to context
                'is_first_message' => $isFirstMessage, // Add flag for first message
                'conversation_history' => $conversationHistory, // Add conversation history
                // Informazioni del profilo (escluso email, username, password)
                'phone' => $store->phone,
                'address' => $store->address,
                'city' => $store->city,
                'state' => $store->state,
                'postal_code' => $store->postal_code,
                'country' => $store->country,
                'website' => $store->website,
            ];

            // Get AI response (first check knowledge base, then use AI with conversation context)
            $aiResponse = $this->geminiService->generateResponse($message, $context, $store);

            // Log the conversation
            $chatLog = ChatLog::create([
                'store_id' => $store->id,
                'qr_code_id' => $qrCode?->id,
                'user_message' => $message,
                'ai_response' => $aiResponse,
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'referer' => $request->header('referer'),
                    'ref_code' => $refCode,
                ],
            ]);

            // Track interaction for analytics
            $this->trackInteraction($store, $request, $message, $aiResponse, $sessionId, $qrCode, $refCode, $latitude, $longitude, $locationData);

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'session_id' => $sessionId,
                'chat_id' => $chatLog->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Chatbot error', [
                'store_id' => $store->id,
                'message' => $message,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore temporaneo del sistema. Riprova tra poco.',
                'session_id' => $sessionId,
            ], 500);
        }
    }

    /**
     * Track QR code scan
     *
     * @param Request $request
     * @param Store $store
     * @return JsonResponse
     */
    public function trackQrScan(Request $request, Store $store): JsonResponse
    {
        $request->validate([
            'ref' => 'required|string',
        ]);

        $refCode = $request->input('ref');

        $qrCode = QrCode::where('ref_code', $refCode)
            ->where('store_id', $store->id)
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'error' => 'QR code not found'
            ], 404);
        }

        // Detect device type
        $userAgent = $request->userAgent();
        $deviceType = 'desktop';
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = preg_match('/iPad/', $userAgent) ? 'tablet' : 'mobile';
        }

        // Create scan record
        QrScan::create([
            'store_id' => $store->id,
            'qr_code_id' => $qrCode->id,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'referer' => $request->header('referer'),
            'device_type' => $deviceType,
            // geo_location will be added later via frontend geolocation API
        ]);

        return response()->json([
            'success' => true,
            'qr_code' => [
                'id' => $qrCode->id,
                'name' => $qrCode->name,
                'question' => $qrCode->question,
            ]
        ]);
    }

    /**
     * Get chat history for a session
     *
     * @param Request $request
     * @param Store $store
     * @return JsonResponse
     */
    public function getChatHistory(Request $request, Store $store): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $sessionId = $request->input('session_id');

        $chatLogs = ChatLog::where('store_id', $store->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->select(['user_message', 'ai_response', 'created_at'])
            ->get();

        return response()->json([
            'success' => true,
            'chat_history' => $chatLogs,
        ]);
    }

    /**
     * Get store information for chatbot
     *
     * @param Store $store
     * @return JsonResponse
     */
    public function getStoreInfo(Store $store): JsonResponse
    {
        return response()->json([
            'success' => true,
            'store' => [
                'name' => $store->name,
                'assistant_name' => $store->assistant_name ?? 'Assistente',
                'chat_theme_color' => $store->chat_theme_color ?? '#10b981',
                'chat_font_family' => $store->chat_font_family ?? 'Inter',
                'chat_ai_tone' => $store->chat_ai_tone ?? 'professional',
                'chat_avatar_image' => $store->chat_avatar_image,
                'chat_suggestions' => $store->chat_suggestions,
                'chat_opening_message' => $store->getOpeningMessage(),
                'opening_hours' => $store->opening_hours,
                'is_active' => $store->is_active,
                'chat_enabled' => $store->chat_enabled,
            ]
        ]);
    }

    /**
     * Save lead from chatbot
     *
     * @param Request $request
     * @param Store $store
     * @return JsonResponse
     */
    public function saveLead(Request $request, Store $store): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'tag' => 'nullable|string|max:255',
            'session_id' => 'nullable|string|max:255',
            // Geographical data
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'country' => 'nullable|string|max:100',
            'country_code' => 'nullable|string|max:2',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:50',
        ]);

        try {
            // Check if lead already exists for this store and email
            $existingLead = \App\Models\Lead::where('store_id', $store->id)
                ->where('email', $request->email)
                ->first();

            if ($existingLead) {
                // Update existing lead with new information
                $existingLead->update([
                    'name' => $request->name ?: $existingLead->name,
                    'whatsapp' => $request->whatsapp ?: $existingLead->whatsapp,
                    'tag' => $request->tag ?: $existingLead->tag,
                    'session_id' => $request->session_id,
                    // Update location data if provided
                    'latitude' => $request->latitude ?: $existingLead->latitude,
                    'longitude' => $request->longitude ?: $existingLead->longitude,
                    'country' => $request->country ?: $existingLead->country,
                    'country_code' => $request->country_code ?: $existingLead->country_code,
                    'region' => $request->region ?: $existingLead->region,
                    'city' => $request->city ?: $existingLead->city,
                    'postal_code' => $request->postal_code ?: $existingLead->postal_code,
                    'timezone' => $request->timezone ?: $existingLead->timezone,
                    'ip_address' => $request->ip(),
                    'subscribed' => true,
                    'last_interaction' => now(),
                    'metadata' => array_merge($existingLead->metadata ?? [], [
                        'updated_via' => 'chatbot',
                        'updated_at' => now()->toISOString(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ])
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Lead updated successfully',
                    'lead_id' => $existingLead->id,
                ]);
            }

            // Create new lead
            $lead = \App\Models\Lead::create([
                'store_id' => $store->id,
                'email' => $request->email,
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'tag' => $request->tag,
                'source' => 'chatbot',
                'session_id' => $request->session_id,
                // Geographical data
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'country' => $request->country,
                'country_code' => $request->country_code,
                'region' => $request->region,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'timezone' => $request->timezone,
                'ip_address' => $request->ip(),
                'subscribed' => true,
                'last_interaction' => now(),
                'metadata' => [
                    'created_via' => 'chatbot',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referer' => $request->header('referer'),
                    'location_method' => $request->latitude ? 'gps' : 'ip',
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead saved successfully',
                'lead_id' => $lead->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Lead save error', [
                'store_id' => $store->id,
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel salvataggio. Riprova più tardi.',
            ], 500);
        }
    }

    /**
     * Track interaction for analytics
     */
    private function trackInteraction($store, $request, $message, $aiResponse, $sessionId, $qrCode = null, $refCode = null, $latitude = null, $longitude = null, $locationData = [])
    {
        try {
            // Parse user agent for device info
            $userAgent = $request->userAgent();
            $deviceType = $this->getDeviceType($userAgent);
            $browser = $this->getBrowser($userAgent);
            $os = $this->getOS($userAgent);

            // Extract UTM parameters
            $utmSource = $request->query('utm_source');
            $utmMedium = $request->query('utm_medium');
            $utmCampaign = $request->query('utm_campaign');

            Interaction::create([
                'store_id' => $store->id,
                'session_id' => $sessionId,
                'question' => $message,
                'answer' => $aiResponse,
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'ref_code' => $refCode,
                'qr_code_id' => $qrCode?->id,
                'device_type' => $deviceType,
                'browser' => $browser,
                'os' => $os,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'city' => $locationData['city'] ?? null,
                'region' => $locationData['region'] ?? null,
                'country' => $locationData['country'] ?? null,
                'country_code' => $locationData['country_code'] ?? null,
                'postal_code' => $locationData['postal_code'] ?? null,
                'timezone' => $locationData['timezone'] ?? null,
                'metadata' => [
                    'referer' => $request->header('referer'),
                    'accept_language' => $request->header('accept-language'),
                    'timestamp' => now()->toISOString(),
                    'location_accuracy' => $locationData['accuracy'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::warning('Analytics tracking failed', [
                'store_id' => $store->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Detect device type from user agent
     */
    private function getDeviceType($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'mobile') !== false ||
            strpos($userAgent, 'android') !== false ||
            strpos($userAgent, 'iphone') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'tablet') !== false ||
                  strpos($userAgent, 'ipad') !== false) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Detect browser from user agent
     */
    private function getBrowser($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }

    /**
     * Detect OS from user agent
     */
    private function getOS($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'mac') !== false) {
            return 'macOS';
        } elseif (strpos($userAgent, 'linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'iOS';
        } else {
            return 'Other';
        }
    }
}
