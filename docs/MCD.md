# MCD — Modèle Conceptuel de Données

## Diagramme des entités

```
┌─────────┐       ┌─────────┐       ┌─────────┐       ┌─────────────────────┐
│  USER   │       │  DOMAIN │       │ CONCEPT │       │ GENERATED_QUESTION  │
├─────────┤       ├─────────┤       ├─────────┤       ├─────────────────────┤
│  id     │──1,N──│  id     │       │  id     │       │  id                 │
│  name   │       │ user_id │──1,N──│ domain_id│──1,N──│  concept_id         │
│  email  │       │  name   │       │  title  │       │  questions (JSON)   │
│ password│       │  color  │       │  expl.  │       │  created_at         │
└─────────┘       └─────────┘       │ diff.   │       └─────────────────────┘
                                    │ status  │
                                    └─────────┘
```

## Relations

- **User → Domain** : 1:N (Un utilisateur possède plusieurs domaines)
- **Domain → Concept** : 1:N (Un domaine contient plusieurs concepts)
- **Concept → GeneratedQuestion** : 1:N (Un concept a plusieurs lots de questions)

## Entités

| Entité | Attributs |
|--------|-----------|
| **User** | id, name, email, password |
| **Domain** | id, user_id, name, color |
| **Concept** | id, domain_id, title, explanation, difficulty, status, deleted_at |
| **GeneratedQuestion** | id, concept_id, questions, created_at, updated_at |