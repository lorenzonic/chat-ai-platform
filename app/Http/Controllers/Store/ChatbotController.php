<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function edit()
    {
        $store = Auth::guard('store')->user();
        return view('store.chatbot.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'assistant_name' => 'required|string|max:50',
            'chat_context' => 'nullable|string|max:2000',
            'chat_theme_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'chat_enabled' => 'boolean',
            'chat_font_family' => 'required|string|max:50',
            'chat_ai_tone' => 'required|in:professional,friendly,cheerful,green_passion',
            'chat_avatar_image' => 'nullable|url|max:500',
            'chat_suggestions' => 'nullable|array|max:6',
            'chat_suggestions.*' => 'string|max:100',
            'chat_opening_message' => 'nullable|string|max:500',
            'opening_hours' => 'nullable|array',
            'opening_hours.*' => 'nullable|array',
            'opening_hours.*.open' => 'nullable|string|max:5',
            'opening_hours.*.close' => 'nullable|string|max:5',
            'opening_hours.*.closed' => 'boolean',
        ]);

        $store = Auth::guard('store')->user();

        // Prepara gli orari di apertura
        $openingHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (isset($request->opening_hours[$day])) {
                $dayData = $request->opening_hours[$day];
                $openingHours[$day] = [
                    'open' => $dayData['open'] ?? null,
                    'close' => $dayData['close'] ?? null,
                    'closed' => isset($dayData['closed']) && $dayData['closed']
                ];
            }
        }

        // Filtra i suggerimenti vuoti
        $suggestions = array_filter($request->chat_suggestions ?? [], function($suggestion) {
            return !empty(trim($suggestion));
        });

        // Se non ci sono suggerimenti personalizzati, usa quelli di default
        if (empty($suggestions)) {
            $suggestions = null; // Il modello restituirà quelli di default
        }

        $store->update([
            'assistant_name' => $request->assistant_name,
            'chat_context' => $request->chat_context,
            'chat_theme_color' => $request->chat_theme_color,
            'chat_enabled' => $request->boolean('chat_enabled'),
            'chat_font_family' => $request->chat_font_family,
            'chat_ai_tone' => $request->chat_ai_tone,
            'chat_avatar_image' => $request->chat_avatar_image,
            'chat_suggestions' => $suggestions,
            'chat_opening_message' => $request->chat_opening_message,
            'opening_hours' => $openingHours,
        ]);

        return redirect()->route('store.chatbot.edit')
            ->with('success', 'Impostazioni chatbot aggiornate con successo!');
    }
}
