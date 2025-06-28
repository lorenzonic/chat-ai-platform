<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * Show the store login form.
     */
    public function showLoginForm(): View
    {
        return view('store.auth.login');
    }

    /**
     * Handle store login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('store')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Get the intended URL or fallback to store dashboard
            $intendedUrl = $request->session()->pull('url.intended', route('store.dashboard'));
            
            // Ensure we're redirecting to a safe store route on the same domain
            $currentHost = $request->getHost();
            $intendedHost = parse_url($intendedUrl, PHP_URL_HOST);
            
            if ($intendedHost !== $currentHost || !str_contains($intendedUrl, '/store/')) {
                $intendedUrl = route('store.dashboard');
            }
            
            return redirect()->to($intendedUrl);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle store logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('store')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.login');
    }
}
