# AGENTS.md

> Fichier obligatoire · Premier commit du projet · Lundi 11/05/2026

---

## Projet

**InterviewPrep** — Application Laravel 13 de préparation aux entretiens techniques.

Permet d'organiser ses connaissances par domaine, rédiger des explications de concepts, suivre son niveau de maîtrise (À revoir / En cours / Maîtrisé) et générer des questions d'entretien via l'API Groq.

---

## Coding Agent

**OpenCode** — [opencode.ai](https://opencode.ai)

```bash
npm install -g opencode-ai
cd interviewprep
opencode
```

OpenCode lit `AGENTS.md` automatiquement au démarrage. Pas besoin de redonner le contexte à chaque session.

---

## Règle fondamentale : Mode Plan AVANT Mode Build

**Toujours dans cet ordre — pas de raccourcis :**

```
ÉTAPE 1 — Lire le spec dans specs/ avant d'ouvrir OpenCode
ÉTAPE 2 — Mode Plan : demander la liste des fichiers à créer, sans générer de code
ÉTAPE 3 — Valider le plan (corriger si fichier inattendu)
ÉTAPE 4 — Mode Build : générer tous les fichiers listés
ÉTAPE 5 — Review : git status + git diff + lire chaque fichier généré
ÉTAPE 6 — Commit avec mention AI explicite
```

### Prompt Mode Plan (à copier-coller)

```
Lis le fichier specs/[NOM-DU-SPEC].md et analyse le projet existant.
En mode Plan, dis-moi exactement quels fichiers tu vas créer ou modifier
pour implémenter cette feature. Liste-les avec une description courte.
Ne génère aucun code pour l'instant.
```

### Prompt Mode Build (après validation du plan)

```
Le plan est correct. Génère maintenant tous les fichiers listés.
Utilise exactement le contenu du spec sans ajouter de fonctionnalités
non demandées.
```

---

## API AI utilisée

**Groq API** — [console.groq.com](https://console.groq.com)

| Propriété | Valeur |
|---|---|
| Modèle | `llama3-8b-8192` |
| Endpoint | `https://api.groq.com/openai/v1/chat/completions` |
| Free tier | Oui — sans CB |

### Règle absolue sur la clé API

```
✅  GROQ_API_KEY dans .env uniquement
✅  .env dans .gitignore (vérifié par défaut dans Laravel)
✅  .env.example avec GROQ_API_KEY= (valeur vide)
❌  Jamais dans le code source
❌  Jamais dans un commit
```

### Appel Http:: — template de référence

```php
Http::withHeaders([
    'Authorization' => 'Bearer ' . config('services.groq.api_key'),
    'Content-Type'  => 'application/json',
])->timeout(30)->post(config('services.groq.url'), [
    'model'    => config('services.groq.model'),
    'messages' => [...],
]);
```

**Zéro package externe.** `Http::` facade Laravel uniquement.

---

## Conventions du projet

### $fillable obligatoire (jamais $guarded)

```php
protected $fillable = ['user_id', 'name', 'color'];  // ✅
protected $guarded = [];                               // ❌ corriger si généré
```

### Création via relation — ownership garanti

```php
auth()->user()->domains()->create($request->validated());  // ✅
Domain::create($request->validated());                     // ❌
```

### Ownership — abort_if obligatoire

```php
abort_if($domain->user_id !== auth()->id(), 403);          // Domain
abort_if($concept->domain_id !== $domain->id, 403);        // Concept (double check)
```

### Eager loading — toujours, jamais de N+1

```php
// Liste domains
->withCount(['concepts', 'concepts as mastered_count' => fn($q) => $q->where('status','mastered')])

// Détail concept
$concept->load('generatedQuestions');
```

### Vues — Accessors uniquement, jamais les valeurs brutes

```blade
{{ $concept->status_label }}      {{-- ✅ "À revoir" --}}
{{ $concept->status }}            {{-- ❌ "to_review" --}}
```

### Vérification routes après chaque génération

```bash
php artisan route:list
```

---

## Ce que l'agent génère bien

| Tâche | Fiabilité |
|---|---|
| Scaffolding controllers resource | ✅ Très bien |
| Form Requests avec règles de base | ✅ Bien |
| Routes RESTful nestées | ✅ Bien |
| Relations Eloquent HasMany/BelongsTo | ✅ Bien |
| Structure des specs markdown | ✅ Bien |

## Ce que je corrige TOUJOURS manuellement

| Ce que l'agent fait | Ce que j'ajoute manuellement |
|---|---|
| `Domain::find($id)` sans ownership | `abort_if($domain->user_id !== auth()->id(), 403)` |
| Pas de `try/catch` sur Groq | Bloc complet + message flash propre |
| Pas de `withCount()` dans index | Eager loading pour éviter N+1 |
| Pas de `load()` dans show | `$concept->load('generatedQuestions')` |
| Messages de validation en anglais | Traduction FR dans les Form Requests |
| `Http::groq()` inexistant | `Http::withHeaders()->post()` standard Laravel |
| `$guarded` au lieu de `$fillable` | Remplacement immédiat |
| Cast JSON manquant | `'questions' => 'array'` dans `$casts` |
| updateStatus() absente | Cycle `array_search()` écrit manuellement |

---

## Format des commits avec mention AI

```
[TAG] Description courte
[AI-assisted: ce que OpenCode a généré | ce que j'ai modifié manuellement et pourquoi]
```

### Tags utilisés

| Tag | Usage |
|---|---|
| `[SETUP]` | Setup projet, premier commit |
| `[SPEC]` | Fichier specs/ ajouté avant le code |
| `[DOMAINS]` | Feature Domains |
| `[CONCEPTS]` | Feature Concepts |
| `[AI]` | Feature Groq API |
| `[FIX]` | Correction manuelle |
| `[QUALITY]` | N+1, Debugbar |
| `[BONUS]` | Features bonus |
| `[TESTING]` | Tests manuels |
| `[DOCS]` | README |

### Exemples réels

```
[SETUP] Initialize Laravel 11 + Breeze + AGENTS.md — first commit

[SPEC] Add specs/domains-crud.md — AI plan before coding

[DOMAINS] Add DomainController + Form Requests
[AI-assisted: controller skeleton généré par OpenCode | withCount() et abort_if() ownership ajoutés manuellement]

[AI] Add GroqService + GeneratedQuestionController
[AI-assisted: structure GroqService générée par OpenCode | Http::groq() remplacé par Http::withHeaders()->post() (méthode inexistante), try/catch + nettoyage backticks ajoutés manuellement]

[QUALITY] Fix N+1 queries — verified with Debugbar
```

---

## Branches Git

```
main
├── feature/domains-crud      → merge après Jour 2
├── feature/concepts-crud     → merge après Jour 3
└── feature/ai-generation     → merge après Jour 4
```

---

## Structure specs/

```
specs/
├── auth.md              ← US1 — Auth Breeze
├── domains-crud.md      ← US2, US3, US4 — CRUD Domains
├── concepts-crud.md     ← US5 à US10 — CRUD Concepts
└── ai-generation.md     ← US11, US12, US13 — Groq API
```

---

*InterviewPrep · Solo Developer · Laravel 13 · Mai 2026*
