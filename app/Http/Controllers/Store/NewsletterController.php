<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\Lead;
use App\Jobs\SendNewsletterJob;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletters.
     */
    public function index(): View
    {
        $store = Auth::guard('store')->user();

        $newsletters = Newsletter::where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $leadsCount = Lead::where('store_id', $store->id)
            ->where('subscribed', true)
            ->count();

        return view('store.newsletters.index', compact('newsletters', 'leadsCount'));
    }

    /**
     * Show the form for creating a new newsletter.
     */
    public function create(): View
    {
        $store = Auth::guard('store')->user();

        $leadsCount = Lead::where('store_id', $store->id)
            ->where('subscribed', true)
            ->count();

        return view('store.newsletters.create', compact('leadsCount'));
    }

    /**
     * Store a newly created newsletter in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $store = Auth::guard('store')->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|url|max:500',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Handle image uploads
        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('newsletters', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        $newsletter = Newsletter::create([
            'store_id' => $store->id,
            'title' => $request->title,
            'content' => $request->content,
            'images' => $imageUrls,
            'cta_text' => $request->cta_text,
            'cta_url' => $request->cta_url,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('store.newsletters.show', $newsletter)
            ->with('success', 'Newsletter creata con successo!');
    }

    /**
     * Display the specified newsletter.
     */
    public function show(Newsletter $newsletter): View
    {
        $store = Auth::guard('store')->user();

        if ($newsletter->store_id !== $store->id) {
            abort(404);
        }

        $leadsCount = Lead::where('store_id', $store->id)
            ->where('subscribed', true)
            ->count();

        return view('store.newsletters.show', compact('newsletter', 'leadsCount'));
    }

    /**
     * Show the form for editing the specified newsletter.
     */
    public function edit(Newsletter $newsletter): View
    {
        $store = Auth::guard('store')->user();

        if ($newsletter->store_id !== $store->id) {
            abort(404);
        }

        return view('store.newsletters.edit', compact('newsletter'));
    }

    /**
     * Update the specified newsletter in storage.
     */
    public function update(Request $request, Newsletter $newsletter): RedirectResponse
    {
        $store = Auth::guard('store')->user();

        if ($newsletter->store_id !== $store->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|url|max:500',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Handle image uploads
        $imageUrls = $newsletter->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('newsletters', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        $newsletter->update([
            'title' => $request->title,
            'content' => $request->content,
            'images' => $imageUrls,
            'cta_text' => $request->cta_text,
            'cta_url' => $request->cta_url,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('store.newsletters.show', $newsletter)
            ->with('success', 'Newsletter aggiornata con successo!');
    }

    /**
     * Remove the specified newsletter from storage.
     */
    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        $store = Auth::guard('store')->user();

        if ($newsletter->store_id !== $store->id) {
            abort(404);
        }

        // Delete uploaded images
        if ($newsletter->images) {
            foreach ($newsletter->images as $imageUrl) {
                $path = str_replace('/storage/', '', $imageUrl);
                Storage::disk('public')->delete($path);
            }
        }

        $newsletter->delete();

        return redirect()->route('store.newsletters.index')
            ->with('success', 'Newsletter eliminata con successo!');
    }

    /**
     * Send newsletter to all leads
     */
    public function send(Newsletter $newsletter): RedirectResponse
    {
        $store = Auth::guard('store')->user();

        if ($newsletter->store_id !== $store->id) {
            abort(404);
        }

        // Check if store can send newsletters (premium feature)
        if (!$store->is_premium) {
            return redirect()->back()
                ->with('error', 'Le newsletter sono una funzionalità premium. Effettua l\'upgrade per inviare newsletter.');
        }

        if ($newsletter->status === 'sent') {
            return redirect()->back()
                ->with('error', 'Questa newsletter è già stata inviata.');
        }

        $leads = Lead::where('store_id', $store->id)
            ->where('subscribed', true)
            ->get();

        if ($leads->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Non ci sono lead iscritti per ricevere la newsletter.');
        }

        // Update newsletter status
        $newsletter->update([
            'status' => 'sending',
            'recipients_count' => $leads->count(),
        ]);

        // Dispatch job to send newsletter (we'll implement this later)
        // SendNewsletterJob::dispatch($newsletter, $leads);

        // For now, mark as sent (we'll implement actual sending later)
        $newsletter->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return redirect()->route('store.newsletters.show', $newsletter)
            ->with('success', "Newsletter marcata come inviata a {$leads->count()} iscritti! (Implementazione invio email in arrivo)");
    }

    /**
     * Preview newsletter
     */
    public function preview(Newsletter $newsletter): View
    {
        $store = Auth::guard('store')->user();

        if ($newsletter->store_id !== $store->id) {
            abort(404);
        }

        return view('store.newsletters.preview', compact('newsletter'));
    }

    /**
     * Show leads management
     */
    public function leads(): View
    {
        $store = Auth::guard('store')->user();

        $leads = Lead::where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Lead::where('store_id', $store->id)->count(),
            'subscribed' => Lead::where('store_id', $store->id)->where('subscribed', true)->count(),
            'unsubscribed' => Lead::where('store_id', $store->id)->where('subscribed', false)->count(),
            'this_month' => Lead::where('store_id', $store->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('store.newsletters.leads', compact('leads', 'stats'));
    }
}
