# InterviewPrep — Plateforme de Préparation aux Entretiens Techniques avec Laravel

## 📌 Présentation du Projet

**InterviewPrep Laravel** est une application personnelle permettant de structurer et suivre sa préparation aux entretiens techniques.

L'utilisateur peut :

- Organiser ses connaissances par domaine technique
- Créer des notes de révision pour chaque concept
- Suivre son niveau de maîtrise
- Générer des questions d'entretien réalistes via une API AI (Groq)

---

# 🛠️ Technologies Utilisées

- HTML5
- CSS3
- PHP
- Laravel
- MySQL
- Git
- API AI (Groq API)
- Scrum / Kanban
- IA Générative

---

# 📚 Référentiel

**[2023] Développeur Web et Web Mobile**

Compétences :
- Backend Laravel
- Architecture MVC
- Gestion base de données
- API REST
- Workflow AI-Assisted

---

# 🎯 Contexte du Projet

Un développeur web marocain vient de terminer sa formation.

Il décroche un entretien technique avec une startup SaaS à Casablanca pour un poste Backend Laravel.

Le problème :

- connaissances dispersées
- absence de structure
- difficulté à mesurer sa maîtrise
- manque d'entraînement réaliste

Il cherche une solution simple pour :

- organiser ses connaissances
- suivre sa progression
- générer des questions d'entretien techniques automatiquement

---

# 🚀 Mission

Développer **InterviewPrep**, une application Laravel permettant :

- la gestion des domaines techniques
- la gestion des concepts
- le suivi de progression
- la génération AI de questions d’entretien via l’API Groq

---

# 👤 User Stories

---

## 🔐 Authentification

### US1 — Inscription / Connexion / Déconnexion

En tant qu'utilisateur, je veux :

- créer un compte
- me connecter
- me déconnecter

---

# 🗂️ Gestion des Domaines

## US2 — Liste des domaines

Afficher :

- tous les domaines techniques
- nombre total de concepts
- nombre de concepts maîtrisés

---

## US3 — Créer un domaine

Créer un domaine avec :

- nom
- couleur de badge

Exemples :

- Laravel ORM
- PHP OOP
- MySQL

---

## US4 — Modifier / Supprimer un domaine

L'utilisateur peut :

- modifier le nom
- modifier la couleur
- supprimer le domaine

---

# ✍️ Gestion des Concepts

## US5 — Liste des concepts

Afficher :

- titre
- difficulté
- statut de maîtrise

Filtres :

- statut
- difficulté

Formats :

### Difficulté
- Junior
- Mid
- Senior

### Statut
- À revoir
- En cours
- Maîtrisé

---

## US6 — Créer un concept

Créer un concept avec :

- titre
- explication
- difficulté
- statut initial

Exemple :

### Titre
`Eloquent N+1 Problem`

---

## US7 — Détail d’un concept

Afficher :

- titre
- explication
- difficulté
- statut
- questions générées

---

## US8 — Modifier un concept

Modifier :

- titre
- explication
- difficulté
- statut

---

## US9 — Changement rapide du statut

Changer rapidement :

- À revoir
- En cours
- Maîtrisé

directement depuis la liste des concepts.

---

## US10 — Supprimer un concept

Supprimer un concept.

---

# 🤖 Génération AI

## US11 — Générer des questions d’entretien

Depuis un concept :

- générer 5 questions techniques réalistes
- utiliser l’API Groq
- baser les questions sur :
  - le titre
  - l’explication

---

## US12 — Historique des générations

Afficher :

- toutes les générations passées
- les 5 questions
- la date de création

---

## US13 — Supprimer une génération

Supprimer un lot de questions générées.

---

# ⭐ Bonus

## Dashboard de progression

Afficher :

- statistiques globales
- concepts par statut
- domaine le mieux maîtrisé
- domaine à revoir

---

## Soft Deletes

Archiver les concepts supprimés.

Fonctionnalités :

- restaurer un concept
- page “Archivés”

---

## Filtres combinés

Filtrer simultanément par :

- statut
- difficulté

---

# ⚠️ Contraintes Techniques Obligatoires

---

# 🧠 Workflow AI-Assisted

Obligatoire :

- fichier `AGENTS.md`
- dossier `specs/`
- commits avec mention AI
- utilisation du mode Plan avant Build

---

# 🤖 Coding Agents Autorisés

- OpenCode
- Claude Code
- Gemini CLI
- GitHub Copilot CLI

---

# 🌐 APIs AI Autorisées

- Groq API
- Google Gemini API
- Mistral API
- Together AI

---

# 📡 Contraintes API AI

Obligatoire :

- utilisation de `Http::` Laravel
- clé API dans `.env`
- gestion propre des erreurs
- sauvegarde en base avant affichage

---

# 📅 Modalités Pédagogiques

- Mode : Individuel
- Durée : 5 jours

### Dates
- Début : 11/05/2026
- Deadline : 15/05/2026

---

# 🧪 Modalités d’Évaluation

## Démo Live

Présentation :

- fonctionnalités
- architecture
- workflow AI

---

## Questions Techniques

Questions sur :

- le code
- Laravel
- API AI
- architecture

---

# 🧠 Démo Workflow AI

Montrer :

- un fichier dans `specs/`
- un commit avec mention AI
- ce que l’agent a généré
- ce qui a été modifié manuellement

---

# 📦 Livrables

---

## 1. Repository GitHub

Obligatoire :

- minimum 15 commits
- commits quotidiens
- branches feature

### Branches
- feature/domains-crud
- feature/concepts-crud
- feature/ai-generation

---

## 2. Jira

Obligatoire :

- board partagé
- user stories en tickets
- historique des mouvements visible

---

## 3. MCD & MLD

### MCD
Entités :

- User
- Domain
- Concept
- GeneratedQuestion

### MLD
Inclure :

- types
- PK
- FK
- relation generated_questions → concepts

---

## 4. Présentation

Structure obligatoire :

1. Titre & Auteur
2. Contexte & Problème
3. MCD
4. MLD
5. Stack & Outils
6. Workflow AI-Assisted
7. Feature AI
8. Démo Live
9. Retour d’expérience AI
10. Conclusion

---

# 📑 Règles des Slides

- maximum 30 mots
- minimum 1 visuel
- police minimum 24px
- numérotation obligatoire

Slides obligatoires :

- MCD
- MLD
- Workflow AI-Assisted

---

# 📂 Dossier specs/

Obligatoire :

- un fichier `.md` par feature
- dossier visible publiquement

---

# 📘 README.md

README obligatoire dans le repository.

---

# ✅ Critères de Performance

---

# 🏗️ Architecture Laravel — 30%

- Relations Eloquent correctes
- Form Requests
- Accessors :
  - statusLabel()
  - difficultyLabel()
- API via Http::
- zéro N+1

---

# ⚙️ Fonctionnalités — 25%

- CRUD Domains
- CRUD Concepts
- changement rapide du statut
- génération AI
- historique des générations

---

# 🤖 Workflow AI-Assisted — 25%

- AGENTS.md
- dossier specs/
- commits AI
- explication du workflow

---

# 🎤 Présentation — 20%

- structure respectée
- screenshots réels
- règles slides respectées

---