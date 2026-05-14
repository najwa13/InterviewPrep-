<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptRequest;
use App\Http\Requests\UpdateConceptRequest;
use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConceptController extends Controller
{
    public function index(Domain $domain): View
    {
        $this->authorizeDomain($domain);

        $query = $domain->concepts();
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('difficulty')) {
            $query->where('difficulty', request('difficulty'));
        }

        $concepts = $query->latest()->get();

        return view('concepts.index', compact('domain', 'concepts'));
    }

    public function create(Domain $domain): View
    {
        $this->authorizeDomain($domain);

        return view('concepts.create', compact('domain'));
    }

    public function store(StoreConceptRequest $request, Domain $domain): RedirectResponse
    {
        $this->authorizeDomain($domain);

        $domain->concepts()->create($request->validated());

        return redirect()->route('domains.concepts.index', $domain)
            ->with('success', 'Concept créé.');
    }

    public function show(Domain $domain, Concept $concept): View
    {
        $this->authorizeDomain($domain);
        $this->authorizeConcept($domain, $concept);

        $concept->load('generatedQuestions');

        return view('concepts.show', compact('domain', 'concept'));
    }

    public function edit(Domain $domain, Concept $concept): View
    {
        $this->authorizeDomain($domain);
        $this->authorizeConcept($domain, $concept);

        return view('concepts.edit', compact('domain', 'concept'));
    }

    public function update(UpdateConceptRequest $request, Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorizeDomain($domain);
        $this->authorizeConcept($domain, $concept);

        $concept->update($request->validated());

        return redirect()->route('domains.concepts.index', $domain)
            ->with('success', 'Concept mis à jour.');
    }

    public function destroy(Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorizeDomain($domain);
        $this->authorizeConcept($domain, $concept);

        $concept->delete();

        return redirect()->route('domains.concepts.index', $domain)
            ->with('success', 'Concept archivé.');
    }

    public function updateStatus(Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorizeDomain($domain);
        $this->authorizeConcept($domain, $concept);

        $statuses = ['to_review', 'in_progress', 'mastered'];
        $currentIndex = array_search($concept->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);
        $nextStatus = $statuses[$nextIndex];

        $concept->update(['status' => $nextStatus]);

        return back()->with('success', 'Statut → ' . $concept->fresh()->status_label);
    }

    public function archived(Domain $domain): View
    {
        $this->authorizeDomain($domain);

        $concepts = $domain->concepts()->onlyTrashed()->latest()->get();

        return view('concepts.archived', compact('domain', 'concepts'));
    }

    public function restore(Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorizeDomain($domain);

        $concept->restore();

        return redirect()->route('domains.concepts.archived', $domain)
            ->with('success', 'Concept restauré.');
    }

    private function authorizeDomain(Domain $domain): void
    {
        abort_if($domain->user_id !== auth()->id(), 403);
    }

    private function authorizeConcept(Domain $domain, Concept $concept): void
    {
        abort_if($concept->domain_id !== $domain->id, 403);
    }
}