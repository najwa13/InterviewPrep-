# MLD — Modèle Logique de Données

## Tables SQL

### users

| Colonne | Type | Contrainte |
|---------|------|------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | NOT NULL, UNIQUE |
| password | VARCHAR(255) | NOT NULL |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### domains

| Colonne | Type | Contrainte |
|---------|------|------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FK → users.id, CASCADE DELETE |
| name | VARCHAR(255) | NOT NULL |
| color | VARCHAR(7) | DEFAULT '#3B82F6' |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### concepts

| Colonne | Type | Contrainte |
|---------|------|------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| domain_id | BIGINT UNSIGNED | FK → domains.id, CASCADE DELETE |
| title | VARCHAR(255) | NOT NULL |
| explanation | TEXT | NOT NULL |
| difficulty | ENUM('junior','mid','senior') | DEFAULT 'junior' |
| status | ENUM('to_review','in_progress','mastered') | DEFAULT 'to_review' |
| deleted_at | TIMESTAMP | NULL (SoftDeletes) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### generated_questions

| Colonne | Type | Contrainte |
|---------|------|------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| concept_id | BIGINT UNSIGNED | FK → concepts.id, CASCADE DELETE |
| questions | JSON | NOT NULL |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

## Contraintes d'intégrité

- **Cascade DELETE** : supprimer un domaine supprime tous ses concepts, qui supprime toutes les questions générées
- **SoftDeletes** : les concepts sont archivés avant suppression définitive
- **Enum** : difficulté et status sont limitées aux valeurs autorisées