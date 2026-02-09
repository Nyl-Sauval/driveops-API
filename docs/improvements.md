# Pistes d'Amélioration (Improvements)

Ce document liste les axes d'amélioration identifiés pour l'API DriveOps.

## 1. Architecture & Design Patterns

### Introduction d'une couche Service
- **Problème** : Logique métier complexe (ex: calcul entretiens futurs) directement dans les Contrôleurs.
- **Solution** : Extraire cette logique dans des Services dédiés (ex: `MaintenanceService`).
- **Bénéfice** : Controllers plus légers, logique réutilisable et testable unitairement via Mocking.

### Utilisation de FormRequests
- **Problème** : Validation `$request->validate([...])` redondante dans chaque méthode de contrôleur.
- **Solution** : Créer des classes `FormRequest` (ex: `StoreVehiculeRequest`, `UpdateUserRequest`).
- **Bénéfice** : Centralisation des règles de validation et messages d'erreur.

### API Resources (JsonResource)
- **Problème** : `return response()->json($model)` expose la structure DB interne.
- **Solution** : Utiliser des `JsonResource` pour transformer les modèles en réponses JSON standardisées.
- **Bénéfice** : Indépendance entre DB et API, gestion facile des formats de date et champs calculés.

## 2. Qualité du Code

### Standardisation du Naming
- **Observation** : Mélange de conventions (ex: `firstName` vs `firstname`).
- **Action** : Adopter `snake_case` pour les champs DB/JSON et `camelCase` pour les variables/méthodes PHP, et s'y tenir strictement.

### Gestion des Erreurs
- **Observation** : Peu de gestion explicite des exceptions.
- **Action** : Implémenter un `Handler` global ou des blocs `try-catch` pour renvoyer des messages d'erreur API structurés (code erreur, message user-friendly).

## 3. Tests
- **État actuel** : Peu ou pas de tests automatisés visibles dans l'analyse structurelle.
- **Priorité 1** : Tests unitaires sur les règles métier (calcul dates/kilométrages).
- **Priorité 2** : Feature tests sur les endpoints API (au moins les cas nominaux 200 OK).

## 4. Fonctionnalités
- **Authentification** : Ajouter une vérification d'email.
- **Uploads** : Implémenter le stockage réel des fichiers (Factures, Avatars) sur S3 ou disque local avec lien public sécurisé.
- **Notifications** : Mettre en place un système de notification (Mail/Notification DB) pour les rappels d'entretien.
