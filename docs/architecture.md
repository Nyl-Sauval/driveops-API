# Architecture & Choix Techniques

Ce document décrit l'organisation technique de l'API DriveOps.

## 1. Vue d'Ensemble
- **Framework** : Laravel (Version récente, support PHP 8+).
- **Type** : API REST Monolithique.
- **Base de Données** : Relationnelle (MySQL / MariaDB / PostgreSQL), gérée via Eloquent ORM.
- **Authentification** : Laravel Sanctum (Tokens Stateful/Stateless).

## 2. Organisation du Code
Le projet suit la structure standard de Laravel :

- **`app/Models`** : Définition des entités et relations (User, Vehicule, Maintenance, Invoice).
- **`app/Http/Controllers/Api`** : Logique de traitement des requêtes HTTP.
    - Les contrôleurs sont regroupés par ressource.
    - Pas de dossier `Services` ou `Repositories` : la logique métier est actuellement implémentée directement dans les contrôleurs.
- **`app/Policies`** : Gestion des autorisations (Authorization) pour s'assurer que les utilisateurs n'accèdent qu'à leurs propres données.
- **`routes/api.php`** : Définition des endpoints API.

## 3. Patterns Utilisés

### MVC (Model-View-Controller)
- **Model** : Eloquent Active Record (`app/Models`).
- **View** : Réponses JSON directes (`response()->json(...)`). Pas de classe `JsonResource` utilisée.
- **Controller** : Point d'entrée de la logique métier (`app/Http/Controllers`).

### Authorization via Policies
- Utilisation systématique des Policies Laravel (`$this->authorize(...)`) dans les méthodes des contrôleurs.

### Validation
- Validation "in-line" dans les contrôleurs (`$request->validate([...])`) plutôt que via des FormRequests séparés.

## 4. Points d'Attention (Dette Technique)
- **Logique dans les Contrôleurs** : Certaines méthodes (ex: `futureMaintenancesByUser`) contiennent des requêtes Eloquent complexes avec des closures qui seraient mieux placées dans des Scopes ou des Services.
- **Absence de DTO / Resources** : Les réponses JSON exposent directement les modèles Eloquent (parfois avec `makeHidden`), ce qui couple la structure de la BDD à l'API publique.
- **Gestion des Erreurs** : Peu de blocs `try-catch` explicites ; l'API repose sur le gestionnaire d'exceptions par défaut de Laravel.
