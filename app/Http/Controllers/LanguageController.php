<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, array_keys(config('app.available_locales')))) {
            return redirect()->back()->with('error', 'Lingua non supportata.');
        }

        // Store locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Get redirect URL from referrer or default to home
        $redirectTo = $request->header('referer', '/');

        // Add success message
        $message = $locale === 'it'
            ? 'Lingua cambiata in italiano con successo!'
            : 'Language successfully changed to English!';

        return redirect($redirectTo)->with('success', $message);
    }

    /**
     * Get current locale
     */
    public function current()
    {
        return response()->json([
            'current_locale' => App::getLocale(),
            'available_locales' => config('app.available_locales'),
        ]);
    }
}
