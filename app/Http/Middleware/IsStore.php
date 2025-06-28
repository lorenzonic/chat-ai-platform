<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('store')->check()) {
            // Store the intended URL if accessing a store route
            if ($request->is('store/*') && !$request->is('store/login')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }
            
            return redirect()->route('store.login');
        }

        return $next($request);
    }
}
