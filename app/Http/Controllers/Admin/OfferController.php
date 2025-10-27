<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Grower;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Offer::with('grower');

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('end_date', '<', Carbon::now());
                    break;
            }
        }

        // Filter by grower
        if ($request->filled('grower_id')) {
            $query->where('grower_id', $request->grower_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $offers = $query->orderBy('created_at', 'desc')->paginate(15);
        $growers = Grower::orderBy('name')->get();

        return view('admin.offers.index', compact('offers', 'growers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $growers = Grower::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.offers.create', compact('growers', 'products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'discount_value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'code' => 'nullable|string|max:50|unique:offers,code',
            'grower_id' => 'nullable|exists:growers,id',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'string',
        ]);

        // Additional validation based on offer type
        if ($request->type === 'percentage') {
            $request->validate([
                'discount_value' => 'required|numeric|min:0|max:100',
            ]);
        } elseif ($request->type === 'fixed_amount') {
            $request->validate([
                'discount_value' => 'required|numeric|min:0',
            ]);
        } elseif ($request->type === 'buy_x_get_y') {
            $request->validate([
                'buy_quantity' => 'required|integer|min:1',
                'get_quantity' => 'required|integer|min:1',
            ]);
        }

        Offer::create($request->all());

        return redirect()
            ->route('admin.offers.index')
            ->with('success', 'Offerta creata con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer): View
    {
        $offer->load(['grower', 'orders']);

        return view('admin.offers.show', compact('offer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer): View
    {
        $growers = Grower::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.offers.edit', compact('offer', 'growers', 'products', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'discount_value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'code' => 'nullable|string|max:50|unique:offers,code,' . $offer->id,
            'grower_id' => 'nullable|exists:growers,id',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'string',
        ]);

        // Additional validation based on offer type
        if ($request->type === 'percentage') {
            $request->validate([
                'discount_value' => 'required|numeric|min:0|max:100',
            ]);
        } elseif ($request->type === 'fixed_amount') {
            $request->validate([
                'discount_value' => 'required|numeric|min:0',
            ]);
        } elseif ($request->type === 'buy_x_get_y') {
            $request->validate([
                'buy_quantity' => 'required|integer|min:1',
                'get_quantity' => 'required|integer|min:1',
            ]);
        }

        $offer->update($request->all());

        return redirect()
            ->route('admin.offers.show', $offer)
            ->with('success', 'Offerta aggiornata con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer): RedirectResponse
    {
        $offer->delete();

        return redirect()
            ->route('admin.offers.index')
            ->with('success', 'Offerta eliminata con successo!');
    }

    /**
     * Toggle offer status
     */
    public function toggleStatus(Offer $offer): RedirectResponse
    {
        $offer->update(['is_active' => !$offer->is_active]);

        $status = $offer->is_active ? 'attivata' : 'disattivata';

        return redirect()
            ->back()
            ->with('success', "Offerta {$status} con successo!");
    }
}
