<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grower;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class GrowerController extends Controller
{
    /**
     * Display a listing of the growers.
     */
    public function index(): View
    {
        $growers = Grower::withCount('products')
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);

        return view('admin.growers.index', compact('growers'));
    }

    /**
     * Show the form for creating a new grower.
     */
    public function create(): View
    {
        return view('admin.growers.create');
    }

    /**
     * Store a newly created grower in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:growers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // If email is provided, we need a password
        if (!empty($validated['email'])) {
            if (empty($validated['password'])) {
                // Use default password if none provided
                $validated['password'] = Hash::make('password123');
            } else {
                $validated['password'] = Hash::make($validated['password']);
            }
        } else {
            // Remove password if no email provided
            unset($validated['password']);
        }

        // Remove password_confirmation from the data
        unset($validated['password_confirmation']);

        $grower = Grower::create($validated);

        $message = 'Coltivatore creato con successo.';
        if (!empty($grower->email)) {
            $message .= ' Può accedere con email: ' . $grower->email;
            if (empty($request->password)) {
                $message .= ' e password: password123';
            }
        }

        return redirect()->route('admin.growers.index')
                        ->with('success', $message);
    }

    /**
     * Display the specified grower.
     */
    public function show(Grower $grower): View
    {
        $grower->load([
            'products', 
            'orderItems.order.store', 
            'orderItems.product'
        ]);

        return view('admin.growers.show', compact('grower'));
    }

    /**
     * Show the form for editing the specified grower.
     */
    public function edit(Grower $grower): View
    {
        return view('admin.growers.edit', compact('grower'));
    }

    /**
     * Update the specified grower in storage.
     */
    public function update(Request $request, Grower $grower): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:growers,email,' . $grower->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // Remove password from validated data if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            // Hash the password before saving
            $validated['password'] = Hash::make($validated['password']);
        }

        // Remove password_confirmation from the data (not needed for update)
        unset($validated['password_confirmation']);

        $grower->update($validated);

        $message = 'Informazioni coltivatore aggiornate con successo.';
        if (!empty($request->password)) {
            $message .= ' Password aggiornata.';
        }

        return redirect()->route('admin.growers.index')
                        ->with('success', $message);
    }

    /**
     * Remove the specified grower from storage.
     */
    public function destroy(Grower $grower): RedirectResponse
    {
        // Check if grower has products
        if ($grower->products()->exists()) {
            return redirect()->route('admin.growers.index')
                            ->with('error', 'Cannot delete grower with existing products.');
        }

        $grower->delete();

        return redirect()->route('admin.growers.index')
                        ->with('success', 'Grower deleted successfully.');
    }
}
