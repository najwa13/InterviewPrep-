# Spec : Concepts CRUD

> US5 → US10 · ConceptController · Généré en Mode Plan avec OpenCode · Mercredi 13/05/2026

---

## Contexte

Un Concept est une note de révision sur un sujet technique (ex: "Eloquent N+1 Problem"). Il appartient à un Domain (nested resource). L'utilisateur peut créer, lister avec filtres, voir le détail, modifier, changer le statut rapidement depuis la liste, et supprimer ses concepts.

---

## Ce que je veux

**US5 — Liste des concepts d'un domain :**
- Titre, `difficulty_label` (Junior / Mid / Senior), `status_label` (À revoir / En cours / Maîtrisé)
- Filtre par statut via `?status=to_review|in_progress|mastered`
- Bonus : filtre combiné `?status=...&difficulty=junior|mid|senior`

**US6 — Créer un concept :**
- Titre (texte)
- Explication (textarea)
- Niveau de difficulté : select junior / mid / senior
- Statut par défaut `to_review` — automatique via la DB, pas de champ dans le formulaire

**US7 — Détail :**
- Titre, explication complète, `difficulty_label`, `status_label`
- Historique des questions générées (chargé avec `load()`)

**US8 — Modifier :**
- Tous les champs éditables : titre, explication, difficulté, statut

**US9 — Changement de statut rapide depuis la liste :**
- Bouton `→ Suivant` : formulaire PATCH sans recharger le formulaire d'édition
- Cycle automatique : `to_review → in_progress → mastered → to_review`

**US10 — Supprimer :**
- Soft delete (trait `SoftDeletes` sur le model)

---

## Ce que je NE veux PAS

- Pas de `Concept::find($id)` direct — toujours via `$domain->concepts()`
- Pas d'ownership vérifié une seule fois — **double vérification** : domain → user ET concept → domain
- Pas de rechargement de page complet pour le statut rapide — PATCH simple suffit, pas de JavaScript/AJAX
- Pas d'affichage des valeurs brutes (`to_review`, `junior`) dans les vues — **toujours les Accessors**
- Pas de `load()` ou `with()` omis sur la page show — N+1 interdit
- Pas de messages de validation en anglais

---

## Routes

```php
Route::middleware('auth')->group(function () {
    // Nested resource
    Route::resource('domains.concepts', ConceptController::class);
    // GET    /domains/{domain}/concepts              → index
    // GET    /domains/{domain}/concepts/create       → create
    // POST   /domains/{domain}/concepts              → store
    // GET    /domains/{domain}/concepts/{concept}    → show
    // GET    /domains/{domain}/concepts/{concept}/edit → edit
    // PUT    /domains/{domain}/concepts/{concept}    → update
    // DELETE /domains/{domain}/concepts/{concept}    → destroy

    // US9 — statut rapide
    Route::patch('domains/{domain}/concepts/{concept}/status',
        [ConceptController::class, 'updateStatus'])
        ->name('domains.concepts.updateStatus');

    // Bonus — soft deletes
    Route::get('domains/{domain}/concepts/archived',
        [ConceptController::class, 'archived'])
        ->name('domains.concepts.archived');
    Route::patch('domains/{domain}/concepts/{concept}/restore',
        [ConceptController::class, 'restore'])
        ->name('domains.concepts.restore');
});
```

---

## Controller — méthodes critiques

```php
// app/Http/Controllers/ConceptController.php

// US5 — avec filtres
public function index(Domain $domain): View
{
    $this->authorizeDomain($domain);

    $query = $domain->concepts();
    if (request('status'))     $query->where('status', request('status'));
    if (request('difficulty'))  $query->where('difficulty', request('difficulty')); // bonus

    $concepts = $query->latest()->get();
    return view('concepts.index', compact('domain', 'concepts'));
}

// US7 — load() OBLIGATOIRE pour éviter N+1
public function show(Domain $domain, Concept $concept): View
{
    $this->authorizeDomain($domain);
    $this->authorizeConcept($domain, $concept);
    $concept->load('generatedQuestions'); // ← ne pas oublier
    return view('concepts.show', compact('domain', 'concept'));
}

// US9 — cycle automatique
public function updateStatus(Domain $domain, Concept $concept): RedirectResponse
{
    $this->authorizeDomain($domain);
    $this->authorizeConcept($domain, $concept);

    $statuses = ['to_review', 'in_progress', 'mastered'];
    $next = $statuses[(array_search($concept->status, $statuses) + 1) % count($statuses)];
    $concept->update(['status' => $next]);

    return back()->with('success', 'Statut → ' . $concept->fresh()->status_label);
}

// US10 — soft delete
public function destroy(Domain $domain, Concept $concept): RedirectResponse
{
    $this->authorizeDomain($domain);
    $this->authorizeConcept($domain, $concept);
    $concept->delete();
    return redirect()->route('domains.concepts.index', $domain)
        ->with('success', 'Concept archivé.');
}

// Double ownership — méthodes privées
private function authorizeDomain(Domain $domain): void
{
    abort_if($domain->user_id !== auth()->id(), 403);
}

private function authorizeConcept(Domain $domain, Concept $concept): void
{
    abort_if($concept->domain_id !== $domain->id, 403);
}
```

---

## Form Requests

### StoreConceptRequest

```php
public function rules(): array
{
    return [
        'title'       => ['required', 'string', 'max:255'],
        'explanation' => ['required', 'string', 'min:20'],
        'difficulty'  => ['required', 'in:junior,mid,senior'],
        // status : pas de champ → défaut 'to_review' en DB
    ];
}

public function messages(): array
{
    return [
        'title.required'       => 'Le titre du concept est obligatoire.',
        'explanation.required' => "L'explication est obligatoire.",
        'explanation.min'      => "L'explication doit faire au moins 20 caractères.",
        'difficulty.required'  => 'Choisissez un niveau de difficulté.',
        'difficulty.in'        => 'Le niveau doit être junior, mid ou senior.',
    ];
}
```

### UpdateConceptRequest — mêmes règles + status

```php
'status' => ['required', 'in:to_review,in_progress,mastered'],
```

---

## Vues — points clés

### concepts/index.blade.php

```blade
{{-- Filtres par statut --}}
@foreach(['to_review' => 'À revoir', 'in_progress' => 'En cours', 'mastered' => 'Maîtrisé'] as $val => $label)
    <a href="{{ route('domains.concepts.index', [$domain, 'status' => $val]) }}"
       class="{{ request('status') === $val ? 'active' : '' }}">
        {{ $label }}
    </a>
@endforeach

{{-- Affichage Accessors — JAMAIS les valeurs brutes --}}
{{ $concept->difficulty_label }}   {{-- "Junior" --}}
{{ $concept->status_label }}       {{-- "À revoir" --}}

{{-- US9 : bouton statut rapide --}}
<form action="{{ route('domains.concepts.updateStatus', [$domain, $concept]) }}" method="POST">
    @csrf @method('PATCH')
    <button type="submit">→ Suivant</button>
</form>
```

### concepts/show.blade.php

```blade
{{-- Affichage Accessors --}}
{{ $concept->difficulty_label }}
{{ $concept->status_label }}

{{-- Bouton génération (US11) --}}
<form action="{{ route('domains.concepts.generate', [$domain, $concept]) }}" method="POST">
    @csrf
    <button type="submit">✨ Générer des questions d'entretien</button>
</form>

{{-- Messages flash --}}
@if(session('error'))  <p class="text-red-600">{{ session('error') }}</p>  @endif
@if(session('success')) <p class="text-green-600">{{ session('success') }}</p> @endif

{{-- Historique des générations (US12 + US13) --}}
@forelse($concept->generatedQuestions->sortByDesc('created_at') as $generation)
    <p>{{ $generation->created_at->format('d/m/Y à H:i') }}</p>
    <ol>@foreach($generation->questions as $q)<li>{{ $q }}</li>@endforeach</ol>
    <form action="{{ route('domains.concepts.questions.destroy', [$domain, $concept, $generation]) }}"
          method="POST">
        @csrf @method('DELETE')
        <button>Supprimer</button>
    </form>
@empty
    <p>Aucune génération pour l'instant.</p>
@endforelse
```

---

## Prompt OpenCode utilisé

```
Lis le fichier specs/concepts-crud.md et analyse le projet existant.
En mode Plan, dis-moi exactement quels fichiers tu vas créer ou modifier.
Ne génère aucun code pour l'instant.
```

**Changement après avoir précisé "Ce que je NE veux PAS" :**  
Sans la section, l'agent proposait une seule vérification ownership (sur le domain uniquement) et affichait `$concept->status` directement dans les vues. Avec la précision, il a listé la double vérification et mentionné les Accessors.

---

## Ce que l'agent a généré

OpenCode a généré le ConceptController complet avec index, create, store, show, edit, update, destroy. Il a utilisé `Concept::findOrFail($id)` — remplacé. Il a affiché `$concept->status` dans la vue index — corrigé.

## Ce que j'ai modifié manuellement

- Double `abort_if()` ownership sur chaque méthode sensible
- Méthode `updateStatus()` avec `array_search()` — l'agent ne l'avait pas générée
- `$concept->load('generatedQuestions')` dans `show()` — absent
- Affichage des Accessors `status_label` et `difficulty_label` dans les vues
- Messages de validation en français

---

## Critères d'acceptation

- [ ] Créer → statut par défaut "À revoir" sans champ dans le formulaire
- [ ] Filtre `?status=mastered` → seulement les maîtrisés
- [ ] Bouton `→ Suivant` → cycle to_review → in_progress → mastered → to_review
- [ ] Détail → `difficulty_label` et `status_label` (jamais les valeurs brutes)
- [ ] Modifier → tous les champs éditables dont le statut
- [ ] Supprimer → soft delete → visible dans `/archived`
- [ ] Restaurer → concept de retour dans la liste
- [ ] Tenter accès concept autre user → 403

---

*Spec générée avec OpenCode · Mode Plan · US5 à US10 · Mercredi 13/05/2026*
