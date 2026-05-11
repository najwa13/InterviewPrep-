# AGENTS.md

## Projet : InterviewPrep — Laravel 11

### Coding Agent utilisé
- OpenCode (opencode.ai) — terminal, open source, gratuit

### Workflow AI-Assisted

1. **Mode Plan d'abord** : avant chaque feature, utiliser l'agent en mode Plan pour générer la spec dans `specs/`
2. **Mode Build ensuite** : utiliser l'agent pour générer le code scaffolding
3. **Review manuelle** : toujours relire, adapter, corriger ce que l'agent génère
4. **Commit avec mention** : chaque commit mentionne si l'IA a été utilisée et ce qui a été modifié

### API AI utilisée
- **Groq API** — console.groq.com (free tier, sans CB, ultra-rapide)
- Modèle : llama3-8b-8192
- Appel via `Http::` facade Laravel — zéro package externe

### Ce que l'agent fait bien
- Scaffolding de controllers et Form Requests
- Structure des routes RESTful
- Relations Eloquent basiques

### Ce que je corrige toujours manuellement
- Ownership scope (vérifier que user ne voit que ses données)
- Gestion d'erreur API (try/catch complet)
- Messages flash en français
- Accessors personnalisés