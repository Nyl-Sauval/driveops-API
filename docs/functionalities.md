# Fonctionnalités (Functionalities)

Ce document dresse l'état des lieux fonctionnel de l'API DriveOps.

## Fonctionnalités Implémentées

### 1. Authentification & Gestion des Utilisateurs
- **Inscription (Register)** : Création de compte avec nom, prénom, email, mot de passe.
- **Connexion (Login)** : Authentification par email/mot de passe (Token Sanctum).
- **Google OAuth** : Connexion/Inscription via Google (token ID vérifié).
- **Gestion de Profil** : Mise à jour des informations personnelles et avatar.
- **Sécurité** : Hachage des mots de passe, Tokens avec expiration (1h), Renouvellement de token.

### 2. Gestion du Parc de Véhicules
- **CRUD Véhicules** : Ajout, modification, suppression et listage des véhicules.
- **Détails** : Marque, modèle, année, kilométrage, plaque d'immatriculation.
- **Règles** : Un véhicule appartient à un seul utilisateur. Suppression en cascade des données liées si l'utilisateur est supprimé.

### 3. Gestion des Entretiens (Maintenance)
- **Types d'entretien** :
    - `mileage` : Basé sur le kilométrage (ex: Vidange tous les 10 000km).
    - `time` : Basé sur le temps (ex: Contrôle technique tous les 2 ans).
    - `one_time` : Ponctuel (ex: Réparation suite à panne).
- **Planification** : Définition d'une date ou d'un kilométrage cible (`scheduled_date`, `scheduled_mileage`).
- **Suivi** : Marquage comme réalisé (`done`), avec date et kilométrage réels.
- **Associations** : Un entretien peut être lié à plusieurs véhicules (relation N-N), bien que l'usage typique soit 1 entretien pour 1 véhicule.

### 4. Gestion des Factures (Invoices)
- **CRUD Factures** : Enregistrement des factures avec montant, date et fichier associé.
- **Liaison Flexible** : Une facture peut être associée à :
    - Un ou plusieurs véhicules.
    - Un ou plusieurs entretiens.

## Logique Métier & Règles Spécifiques

### Calcul des Échéances
L'API expose des endpoints spécifiques pour identifier les entretiens à venir ou en retard.

- **Entretiens Futurs (`futureMaintenancesByUser`)** :
    - Critère : La date prévue est dans le futur (`scheduled_date > now`).
    - **OU** Le kilométrage prévu est supérieur au kilométrage actuel du véhicule lié.

- **Entretiens en Retard (`lateMaintenancesByUser`)** :
    - Critère : La date prévue est passée (`scheduled_date < now`).
    - **OU** Le kilométrage prévu est inférieur ou égal au kilométrage actuel du véhicule lié.

### Statistiques
- Comptage du nombre de véhicules par utilisateur (`/users/{id}/vehicles/count`).

## Fonctionnalités Manquantes / À Prévoir
- **Upload de Fichiers** : Le champ `file_path` existe pour les factures, mais la logique de stockage (S3, Local) n'est pas explicitée dans les contrôleurs analysés (géré simplement comme string pour l'instant ou via un trait non vu).
- **Notifications** : Pas de système de notification (email/push) pour les rappels d'entretien implémenté dans l'API actuelle.
- **Rôles Avancés** : Le champ `role` existe mais n'est pas utilisé pour des permissions complexes (seul `UserPolicy` vérifie l'appartenance des données).
