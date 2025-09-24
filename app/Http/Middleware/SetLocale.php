<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is passed in URL
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if (in_array($locale, array_keys(config('app.available_locales')))) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        }
        // Otherwise use session locale
        else if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, array_keys(config('app.available_locales')))) {
                App::setLocale($locale);
            }
        }
        // Default to configured locale
        else {
            App::setLocale(config('app.locale'));
            Session::put('locale', config('app.locale'));
        }

        return $next($request);
    }
}
