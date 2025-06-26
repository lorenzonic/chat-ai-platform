<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreKnowledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeController extends Controller
{
    public function index()
    {
        $store = Auth::guard('store')->user();
        $knowledgeItems = $store->knowledgeItems()->byPriority()->paginate(10);

        return view('store.knowledge.index', compact('knowledgeItems'));
    }

    public function create()
    {
        return view('store.knowledge.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'nullable|string',
            'priority' => 'integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        $store = Auth::guard('store')->user();

        // Processa le keywords
        $keywords = [];
        if ($request->keywords) {
            $keywords = array_map('trim', explode(',', $request->keywords));
            $keywords = array_filter($keywords); // Rimuove elementi vuoti
        }

        $store->knowledgeItems()->create([
            'question' => $request->question,
            'answer' => $request->answer,
            'keywords' => $keywords,
            'priority' => $request->priority ?? 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('store.knowledge.index')
            ->with('success', 'Knowledge entry added successfully!');
    }

    public function edit(StoreKnowledge $knowledge)
    {
        // Verifica che l'elemento appartenga al store corrente
        if ($knowledge->store_id !== Auth::guard('store')->id()) {
            abort(403);
        }

        return view('store.knowledge.edit', compact('knowledge'));
    }

    public function update(Request $request, StoreKnowledge $knowledge)
    {
        // Verifica che l'elemento appartenga al store corrente
        if ($knowledge->store_id !== Auth::guard('store')->id()) {
            abort(403);
        }

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'nullable|string',
            'priority' => 'integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        // Processa le keywords
        $keywords = [];
        if ($request->keywords) {
            $keywords = array_map('trim', explode(',', $request->keywords));
            $keywords = array_filter($keywords);
        }

        $knowledge->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'keywords' => $keywords,
            'priority' => $request->priority ?? 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('store.knowledge.index')
            ->with('success', 'Knowledge entry updated successfully!');
    }

    public function destroy(StoreKnowledge $knowledge)
    {
        // Verifica che l'elemento appartenga al store corrente
        if ($knowledge->store_id !== Auth::guard('store')->id()) {
            abort(403);
        }

        $knowledge->delete();

        return redirect()->route('store.knowledge.index')
            ->with('success', 'Knowledge entry deleted successfully!');
    }
}
