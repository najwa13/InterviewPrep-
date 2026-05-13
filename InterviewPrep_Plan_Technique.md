# InterviewPrep — Plan de Planification Technique

> Laravel 11 · API Groq · Solo Developer · 5 jours (11–15 mai 2026)  
> Lancement : Lundi 11/05 à 10h00 · Deadline : Vendredi 15/05 à 13h00

---

## Vue d'ensemble

| Élément | Détail |
|---|---|
| **Projet** | InterviewPrep — préparation aux entretiens techniques |
| **Stack** | Laravel 11 · MySQL · Blade · Tailwind CSS |
| **API AI** | Groq API — `llama3-8b-8192` via `Http::` facade |
| **Coding agent** | OpenCode (opencode.ai) — terminal, gratuit, open source |
| **Workflow** | Mode Plan → spec validée → Mode Build → review → commit |
| **Branches** | `feature/domains-crud` · `feature/concepts-crud` · `feature/ai-generation` |
| **Commits min** | 15 avec mention AI explicite |

---

## MCD — Modèle Conceptuel de Données

```
USER ──────< DOMAIN ──────< CONCEPT ──────< GENERATED_QUESTION
  1,N              1,N               1,N
```

**Entités (sans types ni FK) :**

| Entité | Attributs |
|---|---|
| **User** | name, email, password |
| **Domain** | name, color |
| **Concept** | title, explanation, difficulty, status |
| **GeneratedQuestion** | questions, created_at |

**Relations :**
- Un User possède plusieurs Domains
- Un Domain contient plusieurs Concepts
- Un Concept a plusieurs GeneratedQuestions

---

## MLD — Modèle Logique de Données

```sql
users
  id            BIGINT UNSIGNED  PK  AUTO_INCREMENT
  name          VARCHAR(255)
  email         VARCHAR(255)     UNIQUE
  password      VARCHAR(255)
  created_at    TIMESTAMP
  updated_at    TIMESTAMP

domains
  id            BIGINT UNSIGNED  PK  AUTO_INCREMENT
  user_id       BIGINT UNSIGNED  FK → users.id  CASCADE DELETE
  name          VARCHAR(255)
  color         VARCHAR(7)
  created_at    TIMESTAMP
  updated_at    TIMESTAMP

concepts
  id            BIGINT UNSIGNED  PK  AUTO_INCREMENT
  domain_id     BIGINT UNSIGNED  FK → domains.id  CASCADE DELETE
  title         VARCHAR(255)
  explanation   TEXT
  difficulty    ENUM('junior','mid','senior')           DEFAULT 'junior'
  status        ENUM('to_review','in_progress','mastered')  DEFAULT 'to_review'
  deleted_at    TIMESTAMP  NULL    ← soft deletes (bonus)
  created_at    TIMESTAMP
  updated_at    TIMESTAMP

generated_questions
  id            BIGINT UNSIGNED  PK  AUTO_INCREMENT
  concept_id    BIGINT UNSIGNED  FK → concepts.id  CASCADE DELETE
  questions     JSON
  created_at    TIMESTAMP
  updated_at    TIMESTAMP
```

---

## Architecture Laravel

### Structure des dossiers

```
interviewprep/
├── AGENTS.md                          ← premier commit obligatoire
├── specs/
│   ├── auth.md
│   ├── domains-crud.md
│   ├── concepts-crud.md
│   └── ai-generation.md
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DomainController.php
│   │   │   ├── ConceptController.php
│   │   │   └── GeneratedQuestionController.php
│   │   └── Requests/
│   │       ├── StoreDomainRequest.php
│   │       ├── UpdateDomainRequest.php
│   │       ├── StoreConceptRequest.php
│   │       └── UpdateConceptRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Domain.php
│   │   ├── Concept.php
│   │   └── GeneratedQuestion.php
│   └── Services/
│       └── GroqService.php
└── resources/views/
    ├── dashboard.blade.php
    ├── domains/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── concepts/
        ├── index.blade.php
        ├── show.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── archived.blade.php
```

### Relations Eloquent

```php
// User.php
public function domains(): HasMany { return $this->hasMany(Domain::class); }

// Domain.php
public function user(): BelongsTo   { return $this->belongsTo(User::class); }
public function concepts(): HasMany  { return $this->hasMany(Concept::class); }

// Concept.php — avec Accessors OBLIGATOIRES
public function domain(): BelongsTo              { return $this->belongsTo(Domain::class); }
public function generatedQuestions(): HasMany     { return $this->hasMany(GeneratedQuestion::class); }

public function getStatusLabelAttribute(): string {
    return match($this->status) {
        'to_review'   => 'À revoir',
        'in_progress' => 'En cours',
        'mastered'    => 'Maîtrisé',
    };
}
public function getDifficultyLabelAttribute(): string {
    return match($this->difficulty) {
        'junior' => 'Junior', 'mid' => 'Mid', 'senior' => 'Senior',
    };
}

// GeneratedQuestion.php
public function concept(): BelongsTo { return $this->belongsTo(Concept::class); }
protected $casts = ['questions' => 'array'];
```

---

## Planning Jour par Jour

---

## JOUR 1 — Lundi 11/05 : Setup & Fondations

**Objectif :** projet opérationnel, DB validée, Auth en place, Jira partagé.

---

### TÂCHE 1.1 — Init projet + AGENTS.md + Git

**Timeline :** 10h00 – 11h00  
**Jira :** `IP-00`  
**Priorité :** Highest ⚠️ (premier commit = AGENTS.md obligatoire)

#### Étapes

```bash
# 1. Créer le projet Laravel 11
composer create-project laravel/laravel interviewprep
cd interviewprep

# 2. Installer Breeze (auth Blade)
composer require laravel/breeze --dev
php artisan breeze:install blade

# 3. Installer Debugbar
composer require --dev barryvdh/laravel-debugbar

npm install && npm run build
```

```bash
# 4. Créer AGENTS.md à la racine (voir fichier dédié)
touch AGENTS.md

# 5. Créer le dossier specs/
mkdir specs && touch specs/.gitkeep

# 6. Configurer .env
# DB_DATABASE=interviewprep + GROQ_API_KEY= (vide)
mysql -u root -e "CREATE DATABASE interviewprep;"
```

```bash
# 7. PREMIER COMMIT
git init
git add .
git commit -m "[SETUP] Initialize Laravel 11 + Breeze + AGENTS.md — first commit"
git remote add origin https://github.com/USERNAME/interviewprep.git
git push -u origin main
```

#### Critères d'acceptation

- [ ] `AGENTS.md` à la racine, commité en **premier**
- [ ] `specs/` créé et commité
- [ ] `php artisan serve` → app accessible sur localhost:8000
- [ ] `/register` et `/login` fonctionnels

---

### TÂCHE 1.2 — MCD / MLD + Migrations

**Timeline :** 11h00 – 13h00  
**Jira :** `IP-01`  
**Priorité :** Highest (MCD/MLD validés AVANT le code)

#### MCD à dessiner (Draw.io ou papier)

```
[User] ——1,N——> [Domain] ——1,N——> [Concept] ——1,N——> [GeneratedQuestion]
```

Exporter en `docs/MCD.png` et `docs/MLD.png` — envoyer pour validation.

#### Créer les migrations

```bash
php artisan make:migration create_domains_table --create=domains
php artisan make:migration create_concepts_table --create=concepts
php artisan make:migration create_generated_questions_table --create=generated_questions
```

**Migration `domains` :**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('name');
$table->string('color', 7)->default('#3B82F6');
$table->timestamps();
```

**Migration `concepts` :**
```php
$table->id();
$table->foreignId('domain_id')->constrained()->cascadeOnDelete();
$table->string('title');
$table->text('explanation');
$table->enum('difficulty', ['junior','mid','senior'])->default('junior');
$table->enum('status', ['to_review','in_progress','mastered'])->default('to_review');
$table->softDeletes();
$table->timestamps();
```

**Migration `generated_questions` :**
```php
$table->id();
$table->foreignId('concept_id')->constrained()->cascadeOnDelete();
$table->json('questions');
$table->timestamps();
```

```bash
php artisan migrate
```

#### Critères d'acceptation

- [ ] `php artisan migrate` sans erreurs
- [ ] MCD/MLD exportés en PNG dans `docs/`
- [ ] MCD/MLD envoyés et validés avant la suite

---

### TÂCHE 1.3 — Models + Relations + Accessors

**Timeline :** 14h00 – 16h00  
**Jira :** `IP-02`

```bash
php artisan make:model Domain
php artisan make:model Concept
php artisan make:model GeneratedQuestion
```

Implémenter les relations et Accessors selon l'architecture définie ci-dessus.  
Points critiques :
- `SoftDeletes` sur `Concept`
- Accessors `status_label` et `difficulty_label` sur `Concept`
- Cast `'questions' => 'array'` sur `GeneratedQuestion`

#### Commit

```
[MODELS] Add Domain, Concept, GeneratedQuestion — Eloquent relations + Accessors
```

---

### TÂCHE 1.4 — Jira Board Setup

**Timeline :** 16h00 – 17h00  
**Jira :** `IP-03`  
**Deadline :** Partagé avec formateur **avant 13h lundi** ⚠️

Créer les tickets :

| Ticket | US | Titre |
|---|---|---|
| IP-10 | US1 | Auth — Inscription / Connexion / Déconnexion |
| IP-11 | US2 | Liste des domaines avec progression |
| IP-12 | US3 | Créer un domaine |
| IP-13 | US4 | Modifier / Supprimer un domaine |
| IP-14 | US5 | Liste des concepts avec filtres |
| IP-15 | US6 | Créer un concept |
| IP-16 | US7 | Voir le détail d'un concept |
| IP-17 | US8 | Modifier un concept |
| IP-18 | US9 | Changement de statut rapide |
| IP-19 | US10 | Supprimer un concept |
| IP-20 | US11 | Générer des questions AI |
| IP-21 | US12 | Historique des générations |
| IP-22 | US13 | Supprimer une génération |
| IP-23 | Bonus | Dashboard de progression |
| IP-24 | Bonus | Soft deletes + page Archivés |
| IP-25 | Bonus | Filtre combiné statut + difficulté |

---

## JOUR 2 — Mardi 12/05 : CRUD Domains

**Objectif :** Feature Domains complète — US2, US3, US4.  
**Branche :** `git checkout -b feature/domains-crud`

---

### TÂCHE 2.1 — Spec + Plan OpenCode

**Timeline :** 09h00 – 10h00

#### Workflow OpenCode — Mode Plan

```bash
cd interviewprep && opencode
```

Prompt Mode Plan :
```
Lis le fichier specs/domains-crud.md et analyse le projet existant.
En mode Plan, dis-moi exactement quels fichiers tu vas créer ou modifier
pour implémenter cette feature. Liste-les avec une description courte.
Ne génère aucun code pour l'instant.
```

Valider la liste. Corriger si nécessaire. Commiter la spec AVANT le code.

```bash
git add specs/domains-crud.md
git commit -m "[SPEC] Add specs/domains-crud.md — AI plan before coding"
```

---

### TÂCHE 2.2 — DomainController + Routes

**Timeline :** 10h00 – 12h00  
**Jira :** `IP-11` `IP-12` `IP-13`

#### Prompt Mode Build

```
Le plan est correct. Génère maintenant tous les fichiers listés.
Utilise exactement le contenu du spec sans ajouter de fonctionnalités
non demandées.
```

#### Routes à ajouter dans `routes/web.php`

```php
Route::middleware('auth')->group(function () {
    Route::resource('domains', DomainController::class);
    Route::resource('domains.concepts', ConceptController::class);
    Route::patch('domains/{domain}/concepts/{concept}/status',
        [ConceptController::class, 'updateStatus'])
        ->name('domains.concepts.updateStatus');
    Route::post('domains/{domain}/concepts/{concept}/generate',
        [GeneratedQuestionController::class, 'store'])
        ->name('domains.concepts.generate');
    Route::delete('domains/{domain}/concepts/{concept}/questions/{question}',
        [GeneratedQuestionController::class, 'destroy'])
        ->name('domains.concepts.questions.destroy');
});
```

#### Points critiques à vérifier après génération

```bash
git diff  # lire chaque fichier généré
```

- [ ] `index()` utilise `withCount(['concepts', 'concepts as mastered_count' => ...])` — sinon N+1
- [ ] `store()` crée via `auth()->user()->domains()->create()` — jamais `Domain::create()`
- [ ] `edit/update/destroy` ont `abort_if($domain->user_id !== auth()->id(), 403)`
- [ ] Routes nommées : `php artisan route:list` pour vérifier

#### Si l'agent a oublié `withCount` — corriger via OpenCode

```
Dans DomainController@index, la query ne charge pas les compteurs.
Ligne actuelle : auth()->user()->domains()->latest()->get()
Remplace par :
auth()->user()->domains()
    ->withCount(['concepts', 'concepts as mastered_count' => fn($q) => $q->where('status','mastered')])
    ->latest()->get()
```

#### Commit

```
[DOMAINS] Add DomainController + routes resource
[AI-assisted: controller skeleton généré par OpenCode depuis specs/domains-crud.md,
withCount et ownership abort_if ajoutés manuellement]
```

---

### TÂCHE 2.3 — Form Requests + Vues Blade Domains

**Timeline :** 14h00 – 17h00  
**Jira :** `IP-11` `IP-12` `IP-13`

```bash
php artisan make:request StoreDomainRequest
php artisan make:request UpdateDomainRequest
```

Règles obligatoires dans les Form Requests :
```php
'name'  => ['required', 'string', 'max:255'],
'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
```

Messages en français — à corriger si l'agent les génère en anglais.

**Vues à créer :**
- `domains/index.blade.php` — badge `style="background-color: {{ $domain->color }}"`, compteurs `concepts_count` et `mastered_count`
- `domains/create.blade.php` — `<input type="color">` avec default `#3B82F6`
- `domains/edit.blade.php` — `@method('PUT')`, valeurs pré-remplies

#### Commit

```
[DOMAINS] Add StoreDomainRequest + UpdateDomainRequest + Blade views
[AI-assisted: vues scaffoldées par OpenCode, messages FR et color picker ajoutés manuellement]
```

**Merge branche :**
```bash
git checkout main && git merge feature/domains-crud
```

---

## JOUR 3 — Mercredi 13/05 : CRUD Concepts

**Objectif :** Feature Concepts complète — US5 à US10.  
**Branche :** `git checkout -b feature/concepts-crud`

---

### TÂCHE 3.1 — Spec + Plan OpenCode

**Timeline :** 09h00 – 10h00

Prompt Mode Plan :
```
Lis le fichier specs/concepts-crud.md et analyse le projet existant.
En mode Plan, dis-moi exactement quels fichiers tu vas créer ou modifier.
Ne génère aucun code pour l'instant.
```

```bash
git add specs/concepts-crud.md
git commit -m "[SPEC] Add specs/concepts-crud.md — AI plan before coding"
```

---

### TÂCHE 3.2 — ConceptController (nested resource)

**Timeline :** 10h00 – 13h00  
**Jira :** `IP-14` à `IP-19`

#### Points critiques à vérifier OBLIGATOIREMENT après génération

```bash
cat app/Http/Controllers/ConceptController.php
```

- [ ] Double ownership : `authorizeDomain($domain)` ET `authorizeConcept($domain, $concept)` sur chaque méthode sensible
- [ ] `show()` : `$concept->load('generatedQuestions')` présent — sinon N+1 sur la page de détail
- [ ] `index()` : filtre `if (request('status'))` présent
- [ ] `updateStatus()` : cycle `to_review → in_progress → mastered → to_review` avec `array_search`

#### Méthode `updateStatus()` — souvent manquante ou incorrecte

Si l'agent ne la génère pas correctement :
```
Dans ConceptController, génère la méthode updateStatus(Domain $domain, Concept $concept).
Elle doit faire cycler le status dans l'ordre : to_review → in_progress → mastered → to_review.
Utilise array_search() pour trouver l'index courant et % count() pour revenir au début.
```

#### Commit

```
[CONCEPTS] Add ConceptController — nested resource + updateStatus cycle
[AI-assisted: ConceptController généré par OpenCode, double ownership et cycle updateStatus écrits manuellement]
```

---

### TÂCHE 3.3 — Form Requests + Vues Concepts

**Timeline :** 14h00 – 17h00  
**Jira :** `IP-14` à `IP-19`

```bash
php artisan make:request StoreConceptRequest
php artisan make:request UpdateConceptRequest
```

**Vues à créer :**
- `concepts/index.blade.php` — filtres `?status=...`, `$concept->status_label`, `$concept->difficulty_label`, bouton PATCH `→ Suivant`
- `concepts/show.blade.php` — détail + bouton génération + historique (US7, US12, US13)
- `concepts/create.blade.php` — select difficulty, pas de champ status (défaut en DB)
- `concepts/edit.blade.php` — select difficulty + select status

⚠️ Ne jamais afficher les valeurs brutes (`to_review`, `junior`). Toujours utiliser les Accessors.

#### Commit

```
[CONCEPTS] Add Form Requests + Blade views — index filters, show detail, status_label
[AI-assisted: vues générées par OpenCode, affichage Accessors et bouton PATCH statut rapide ajoutés manuellement]
```

**Merge branche :**
```bash
git checkout main && git merge feature/concepts-crud
```

---

## JOUR 4 — Jeudi 14/05 : Feature AI + Bonus

**Objectif :** Intégration Groq API — US11, US12, US13 + Dashboard bonus.  
**Branche :** `git checkout -b feature/ai-generation`

---

### TÂCHE 4.1 — Spec + Plan OpenCode

**Timeline :** 09h00 – 10h00

Prompt Mode Plan :
```
Lis le fichier specs/ai-generation.md et analyse le projet existant.
En mode Plan, dis-moi exactement quels fichiers tu vas créer.
Ne génère aucun code pour l'instant.
```

```bash
git add specs/ai-generation.md
git commit -m "[SPEC] Add specs/ai-generation.md — AI plan before coding"
```

---

### TÂCHE 4.2 — Configuration Groq + GroqService

**Timeline :** 10h00 – 12h00  
**Jira :** `IP-20`

```env
# .env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxx
```

```php
// config/services.php
'groq' => [
    'api_key' => env('GROQ_API_KEY'),
    'url'     => 'https://api.groq.com/openai/v1/chat/completions',
    'model'   => 'llama3-8b-8192',
],
```

```bash
mkdir app/Services && touch app/Services/GroqService.php
```

#### Points critiques à vérifier après génération

- [ ] Appel via `Http::withHeaders()->post()` — PAS `Http::groq()` (méthode inexistante)
- [ ] `try/catch` complet autour de l'appel
- [ ] Nettoyage des backticks : `preg_replace('/```json|```/', '', $content)`
- [ ] Validation de la structure JSON : `isset($decoded['questions'])`
- [ ] `$concept->generatedQuestions()->create(['questions' => $questions])` AVANT le redirect

#### Prompt de correction si `Http::groq()` est généré

```
L'agent a utilisé Http::groq() qui n'existe pas dans Laravel.
Remplace par Http::withHeaders(['Authorization' => 'Bearer ' . config('services.groq.api_key'), 'Content-Type' => 'application/json'])->timeout(30)->post(config('services.groq.url'), [...])
```

#### Commit

```
[AI] Add GroqService — Http:: facade + Groq API integration
[AI-assisted: structure GroqService générée par OpenCode, Http::groq() remplacé par Http::withHeaders()->post(),
try/catch et nettoyage backticks ajoutés manuellement]
```

---

### TÂCHE 4.3 — GeneratedQuestionController

**Timeline :** 12h00 – 14h00  
**Jira :** `IP-20` `IP-21` `IP-22`

```bash
php artisan make:controller GeneratedQuestionController
```

Méthodes à implémenter :
- `store(Domain, Concept)` — génère + sauvegarde en base + redirect avec message
- `destroy(Domain, Concept, GeneratedQuestion)` — triple ownership + suppression

#### Commit

```
[AI] Add GeneratedQuestionController — store + destroy
[AI-assisted: controller généré par OpenCode, triple abort_if ownership ajouté manuellement]
```

---

### TÂCHE 4.4 — Dashboard Bonus

**Timeline :** 14h00 – 16h00  
**Jira :** `IP-23`

Stats à calculer (dans le controller ou directement dans la route) :
- Total concepts
- Concepts par statut (to_review, in_progress, mastered)
- Domaine le mieux maîtrisé
- Domaine le plus à revoir

**Soft Deletes + Page Archivés :**
- Route `GET /domains/{domain}/concepts/archived`
- Route `PATCH /domains/{domain}/concepts/{concept}/restore`
- Vue `concepts/archived.blade.php`

#### Commit

```
[BONUS] Add dashboard stats + archived concepts + soft delete restore
```

**Merge branche :**
```bash
git checkout main && git merge feature/ai-generation
```

---

## JOUR 5 — Vendredi 15/05 : Polish & Livraison

**Objectif :** Zéro N+1, tests manuels, présentation prête, tout livré avant 13h.

---

### TÂCHE 5.1 — Debugbar + Correction N+1

**Timeline :** 09h00 – 10h30

Ouvrir Debugbar → onglet **Queries** sur chaque page :

| Page | Problème fréquent | Fix |
|---|---|---|
| `domains/index` | N+1 sur concepts count | `withCount(['concepts', 'concepts as mastered_count' => ...])` |
| `concepts/show` | N+1 sur generatedQuestions | `$concept->load('generatedQuestions')` |
| `dashboard` | N+1 sur tous les domaines | `with('concepts')` dans la query |

```bash
# Vérifier qu'il n'y a pas de dd() oubliés
grep -r "dd\(\|var_dump\|dump\(" app/ resources/
```

#### Commit

```
[QUALITY] Fix N+1 queries with eager loading — verified with Debugbar
```

---

### TÂCHE 5.2 — Tests manuels complets

**Timeline :** 10h30 – 12h00

#### Checklist Auth (US1)
- [ ] Inscription → dashboard
- [ ] Connexion → dashboard
- [ ] Déconnexion → /login
- [ ] Accès `/domains` sans connexion → redirect `/login`

#### Checklist Domains (US2–US4)
- [ ] Créer → badge couleur visible dans la liste
- [ ] Compteurs (X concepts, Y maîtrisés) corrects
- [ ] Modifier → formulaire pré-rempli
- [ ] Supprimer → CASCADE supprime les concepts
- [ ] Accès domain autre user → 403

#### Checklist Concepts (US5–US10)
- [ ] Créer → statut par défaut "À revoir"
- [ ] Filtre `?status=mastered` → seulement les maîtrisés
- [ ] Bouton `→ Suivant` → cycle to_review → in_progress → mastered → to_review
- [ ] Affiche `status_label` et `difficulty_label` (jamais les valeurs brutes)
- [ ] Modifier → tous les champs éditables
- [ ] Supprimer → soft delete → visible dans /archived
- [ ] Restaurer → concept de retour dans la liste

#### Checklist AI (US11–US13)
- [ ] Cliquer "Générer" → 5 questions dans l'historique
- [ ] `SELECT * FROM generated_questions` → questions en JSON
- [ ] 2e génération → 2 entrées distinctes
- [ ] Supprimer une génération → les autres restent
- [ ] Clé API invalide → message flash propre, pas de page blanche

#### Commit

```
[TESTING] Manual tests passed — all US validated, code cleanup done
```

---

### TÂCHE 5.3 — Livraison finale

**Timeline :** 12h00 – 13h00

```bash
# Vérifier 15+ commits
git log --oneline | wc -l

# Vérifier les 3 branches
git branch -a

# Vérifier specs/
ls specs/
# auth.md  domains-crud.md  concepts-crud.md  ai-generation.md

# README.md
touch README.md  # (voir contenu ci-dessous)

git add .
git commit -m "[DOCS] Add complete README.md"
git push origin main
```

#### README.md minimal requis

```markdown
# InterviewPrep

Plateforme de préparation aux entretiens techniques Laravel 11.

## Installation
1. `composer install`
2. `cp .env.example .env` + configurer DB + GROQ_API_KEY
3. `php artisan key:generate && php artisan migrate`
4. `npm install && npm run build && php artisan serve`

## Stack
Laravel 11 · MySQL · Blade · Tailwind · Groq API · OpenCode

## Branches
- `feature/domains-crud`
- `feature/concepts-crud`
- `feature/ai-generation`
```

#### Checklist livraison finale

- [ ] Repository GitHub public avec 15+ commits
- [ ] 3 branches features visibles
- [ ] `specs/` avec 4 fichiers `.md` publiquement accessibles
- [ ] `AGENTS.md` à la racine
- [ ] `README.md` complet
- [ ] Board Jira — tous les tickets en Done
- [ ] Présentation prête (11 slides, structure respectée)
- [ ] **Push final avant 13h00**

---

## Format des commits — Référence rapide

```
[SETUP]   message — setup projet, premier commit
[SPEC]    message — fichier specs/ ajouté AVANT le code
[DOMAINS] message [AI-assisted: ce que l'agent a généré | ce que j'ai ajouté manuellement]
[CONCEPTS] message [AI-assisted: ...]
[AI]      message [AI-assisted: ...]
[FIX]     message — correction manuelle
[QUALITY] message — N+1, Debugbar
[BONUS]   message — features bonus
[TESTING] message — tests manuels
[DOCS]    message — README
```

---

## Checklist des critères de performance

### Architecture Laravel (30%)
- [ ] Relations Eloquent 4 niveaux : User → Domain → Concept → GeneratedQuestion
- [ ] Form Request classes pour toutes les validations
- [ ] Accessors `status_label` et `difficulty_label` utilisés dans les vues
- [ ] Appel API via `Http::` facade — résultat sauvegardé en base avant affichage
- [ ] Zéro N+1 vérifié avec Debugbar

### Fonctionnalités (25%)
- [ ] CRUD Domains complet (US2–US4)
- [ ] CRUD Concepts complet avec changement de statut rapide (US5–US10)
- [ ] Génération AI fonctionnelle — questions affichées et sauvegardées (US11)
- [ ] Historique des générations visible et supprimable (US12–US13)

### Workflow AI-Assisted (25%)
- [ ] `AGENTS.md` présent, complet, premier commit du Jour 1
- [ ] Dossier `specs/` avec ≥ 3 fichiers `.md`
- [ ] Messages de commits avec mention claire de l'usage AI
- [ ] Capable d'expliquer chaque ligne du code généré à l'oral

### Présentation (20%)
- [ ] Structure : Titre → Contexte → MCD → MLD → Stack → Workflow AI → Feature AI → Démo → Bilan agent → Conclusion
- [ ] ≤ 30 mots/slide · ≥ 1 visuel/slide · police ≥ 24px · numérotation visible
- [ ] Slides MCD et MLD obligatoires
- [ ] Slide Workflow AI avec screenshots réels de tes specs et commits

---

*InterviewPrep — Plan Technique Solo Developer · 11–15 mai 2026*
