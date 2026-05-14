<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(): View
    {
        $domains = auth()->user()
            ->domains()
            ->withCount([
                'concepts',
                'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')
            ])
            ->latest()
            ->get();

        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        return view('domains.create');
    }

    public function store(StoreDomainRequest $request): RedirectResponse
    {
        auth()->user()->domains()->create($request->validated());

        return redirect()->route('domains.index')->with('success', 'Domaine créé.');
    }

    public function edit(Domain $domain): View
    {
        $this->checkOwnership($domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $this->checkOwnership($domain);

        $domain->update($request->validated());

        return redirect()->route('domains.index')->with('success', 'Domaine mis à jour.');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $this->checkOwnership($domain);

        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domaine supprimé.');
    }

    private function checkOwnership(Domain $domain): void
    {
        abort_if($domain->user_id !== auth()->id(), 403);
    }
}