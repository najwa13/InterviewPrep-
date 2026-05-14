# Spec : Domains CRUD

> US2, US3, US4 · DomainController · Généré en Mode Plan avec OpenCode · Mardi 12/05/2026

---

## Contexte

Un Domain est un regroupement de concepts techniques (ex: "Laravel ORM", "PHP OOP", "MySQL"). Chaque Domain appartient à un User (relation BelongsTo). L'utilisateur peut créer, lister, modifier et supprimer ses domains. La liste doit afficher la progression (concepts total / maîtrisés) d'un coup d'œil.

---

## Ce que je veux

**US2 — Liste des domains :**
- Nom du domain
- Badge avec la couleur personnalisée (`style="background-color: {{ $domain->color }}"`)
- Nombre total de concepts du domain
- Nombre de concepts avec `status = mastered`
- Lien vers la liste des concepts du domain

**US3 — Créer un domain :**
- Champ nom (texte libre)
- Color picker hexadécimal (valeur par défaut `#3B82F6`)

**US4 — Modifier / Supprimer :**
- Formulaire pré-rempli pour modifier nom et couleur
- Suppression avec confirmation `onsubmit="return confirm(...)"` — CASCADE sur les concepts

---

## Ce que je NE veux PAS

- Pas de `Domain::find($id)` direct — toujours `auth()->user()->domains()` pour garantir l'ownership
- Pas d'accès possible aux domains d'un autre utilisateur — `abort_if(403)` sur edit/update/destroy
- Pas de soft delete sur Domain — suppression définitive avec CASCADE
- Pas de pagination sur la liste
- Pas de route `show` pour les domains — le clic va vers la liste des concepts
- Pas de messages de validation en anglais

---

## Routes

```php
Route::middleware('auth')->group(function () {
    Route::resource('domains', DomainController::class)->except(['show']);
    // GET    /domains           → index
    // GET    /domains/create    → create
    // POST   /domains           → store
    // GET    /domains/{domain}/edit → edit
    // PUT    /domains/{domain}  → update
    // DELETE /domains/{domain}  → destroy
});
```

---

## Controller — méthodes critiques

```php
// app/Http/Controllers/DomainController.php

// US2 — withCount OBLIGATOIRE pour éviter N+1
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

// US3 — créer via relation, jamais Domain::create()
public function store(StoreDomainRequest $request): RedirectResponse
{
    auth()->user()->domains()->create($request->validated());
    return redirect()->route('domains.index')->with('success', 'Domaine créé.');
}

// US4 — ownership obligatoire
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
```

---

## Form Requests

### StoreDomainRequest

```php
public function authorize(): bool { return true; }

public function rules(): array
{
    return [
        'name'  => ['required', 'string', 'max:255'],
        'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
    ];
}

public function messages(): array
{
    return [
        'name.required'  => 'Le nom du domaine est obligatoire.',
        'color.required' => 'Choisissez une couleur pour ce domaine.',
        'color.regex'    => 'La couleur doit être un code hexadécimal valide (ex: #3B82F6).',
    ];
}
```

**UpdateDomainRequest** — mêmes règles.

---

## Vues

### domains/index.blade.php — points clés

```blade
{{-- Badge couleur --}}
<span class="inline-block w-4 h-4 rounded-full"
      style="background-color: {{ $domain->color }}"></span>

{{-- Compteurs US2 --}}
{{ $domain->concepts_count }} concept(s) — {{ $domain->mastered_count }} maîtrisé(s)

{{-- Lien vers concepts --}}
<a href="{{ route('domains.concepts.index', $domain) }}">{{ $domain->name }}</a>

{{-- Suppression avec confirmation --}}
<form action="{{ route('domains.destroy', $domain) }}" method="POST"
      onsubmit="return confirm('Supprimer ce domaine et tous ses concepts ?')">
    @csrf @method('DELETE')
    <button>Supprimer</button>
</form>
```

### domains/create.blade.php — color picker

```blade
<input type="color" name="color" value="{{ old('color', '#3B82F6') }}">
```

### domains/edit.blade.php — pré-remplissage

```blade
<input type="text" name="name" value="{{ old('name', $domain->name) }}">
<input type="color" name="color" value="{{ old('color', $domain->color) }}">
@method('PUT')
```

---

## Prompt OpenCode utilisé

```
Lis le fichier specs/domains-crud.md et analyse le projet existant.
En mode Plan, dis-moi exactement quels fichiers tu vas créer ou modifier.
Ne génère aucun code pour l'instant.
```

**Changement après avoir précisé "Ce que je NE veux PAS" :**  
Sans cette section, l'agent proposait `Domain::findOrFail($id)` dans edit/update/destroy et ne mentionnait pas `withCount()` dans index. Avec la précision, il a listé `abort_if()` et mentionné la nécessité de passer par `auth()->user()->domains()`.

---

## Ce que l'agent a généré

OpenCode a généré le squelette du DomainController avec les 6 méthodes, les Form Requests et les vues Blade avec `@forelse`.

## Ce que j'ai modifié manuellement

- `Domain::findOrFail($id)` → route model binding + `abort_if()` ownership
- Ajout de `withCount(['concepts', 'concepts as mastered_count' => ...])` dans `index()`
- Color picker `<input type="color">` — l'agent avait généré un champ texte
- Messages de validation traduits en français

---

## Critères d'acceptation

- [ ] Liste domains : badge couleur, X concepts, Y maîtrisés
- [ ] Créer domain → apparaît dans la liste avec la bonne couleur
- [ ] Modifier → formulaire pré-rempli, changements sauvegardés
- [ ] Supprimer → CASCADE supprime les concepts associés
- [ ] Accès au domain d'un autre user → 403

---

*Spec générée avec OpenCode · Mode Plan · US2, US3, US4 · Mardi 12/05/2026*
