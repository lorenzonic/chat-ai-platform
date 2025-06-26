<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the store profile page.
     */
    public function show(): View
    {
        $store = auth('store')->user();
        return view('store.profile.show', compact('store'));
    }

    /**
     * Show the form for editing the store profile.
     */
    public function edit(): View
    {
        $store = auth('store')->user();
        return view('store.profile.edit', compact('store'));
    }

    /**
     * Update the store profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $store = auth('store')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:stores,email,' . $store->id,
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
        ]);

        $store->update([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'website' => $request->website,
        ]);

        return redirect()
            ->route('store.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword(): View
    {
        return view('store.profile.password');
    }

    /**
     * Update the store password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|current_password:store',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $store = auth('store')->user();
        $store->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('store.profile.show')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the store account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password:store',
        ]);

        $store = auth('store')->user();

        // Optional: Add logic to handle related data (chatbot settings, QR codes, etc.)
        // You might want to soft delete or transfer ownership

        auth('store')->logout();
        $store->delete();

        return redirect('/')
            ->with('success', 'Your account has been deleted successfully.');
    }
}
