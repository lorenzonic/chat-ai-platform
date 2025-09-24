<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Store;
use App\Models\Grower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * Display a listing of all accounts
     */
    public function index()
    {
        $stores = Store::latest()->paginate(10);
        $admins = Admin::latest()->paginate(10);
        $growers = Grower::latest()->paginate(10);

        return view('admin.accounts.index', compact('stores', 'admins', 'growers'));
    }

    /**
     * Show the form for creating a new store account
     */
    public function createStore()
    {
        return view('admin.accounts.create-store');
    }

    /**
     * Store a newly created store account in storage
     */
    public function storeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:stores',
            'password' => 'required|string|min:8|confirmed',
            'slug' => 'required|string|max:255|unique:stores|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('store-logos', 'public');
        }
        Store::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'slug' => $request->slug,
            'description' => $request->description,
            'website' => $request->website,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'is_active' => $request->boolean('is_active', true),
            'is_premium' => $request->boolean('is_premium', false),
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Store account created successfully!');
    }

    /**
     * Show the form for creating a new admin account
     */
    public function createAdmin()
    {
        return view('admin.accounts.create-admin');
    }

    /**
     * Store a newly created admin account in storage
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,super_admin',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Admin account created successfully!');
    }

    /**
     * Show the specified store account
     */
    public function showStore(Store $store)
    {
        return view('admin.accounts.show-store', compact('store'));
    }

    /**
     * Show the specified admin account
     */
    public function showAdmin(Admin $admin)
    {
        return view('admin.accounts.show-admin', compact('admin'));
    }

    /**
     * Show the form for editing the specified store account
     */
    public function editStore(Store $store)
    {
        return view('admin.accounts.edit-store', compact('store'));
    }

    /**
     * Update the specified store account in storage
     */
    public function updateStore(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('stores')->ignore($store->id)],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('stores')->ignore($store->id)],
            'description' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'slug' => $request->slug,
            'description' => $request->description,
            'website' => $request->website,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'is_active' => $request->boolean('is_active'),
            'is_premium' => $request->boolean('is_premium'),
        ];

        if ($request->hasFile('logo')) {
            // Cancella il vecchio logo se esiste
            if ($store->logo && \Storage::disk('public')->exists($store->logo)) {
                \Storage::disk('public')->delete($store->logo);
            }
            $updateData['logo'] = $request->file('logo')->store('store-logos', 'public');
        }

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $updateData['password'] = Hash::make($request->password);
        }

        $store->update($updateData);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Store account updated successfully!');
    }

    /**
     * Remove the specified store account from storage
     */
    public function destroyStore(Store $store)
    {
        $store->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Store account deleted successfully!');
    }

    /**
     * Toggle store active status
     */
    public function toggleStoreStatus(Store $store)
    {
        $store->update(['is_active' => !$store->is_active]);

        $status = $store->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Store account {$status} successfully!");
    }

    /**
     * Toggle store premium status
     */
    public function toggleStorePremium(Store $store)
    {
        $store->update(['is_premium' => !$store->is_premium]);

        $status = $store->is_premium ? 'upgraded to premium' : 'downgraded from premium';
        return redirect()->back()
            ->with('success', "Store account {$status} successfully!");
    }
}
