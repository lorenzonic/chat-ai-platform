<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grower;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
        ]);

        Grower::create($validated);

        return redirect()->route('admin.growers.index')
                        ->with('success', 'Grower created successfully.');
    }

    /**
     * Display the specified grower.
     */
    public function show(Grower $grower): View
    {
        $grower->load(['products.store']);

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
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
        ]);

        $grower->update($validated);

        return redirect()->route('admin.growers.index')
                        ->with('success', 'Grower updated successfully.');
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
