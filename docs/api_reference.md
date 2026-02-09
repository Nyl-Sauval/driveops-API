# Référence API (API Reference)

Base URL: `/api`
Authentification: Bearer Token (Sanctum) via header `Authorization: Bearer <token>` (sauf endpoints publics).

## Authentification

| Méthode | Endpoint | Description | Auth | Paramètres (Body) |
| :--- | :--- | :--- | :--- | :--- |
| `POST` | `/register` | Créer un compte | Non | `firstName`, `lastName`, `email`, `password`, `password_confirmation` |
| `POST` | `/login` | Se connecter | Non | `email`, `password` |
| `POST` | `/google-login` | Connexion Google OAuth | Non | `token` (ID Token Google) |
| `POST` | `/logout` | Se déconnecter | Oui | - |
| `POST` | `/refresh-token` | Renouveler le token | Oui | - |
| `GET` | `/me` | Info utilisateur connecté | Oui | - |

## Utilisateurs (Users)

| Méthode | Endpoint | Description | Auth | Paramètres / Notes |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/users` | Liste des utilisateurs | Oui | Admin/All (Policy `viewAny`) |
| `GET` | `/users/{id}` | Détails d'un utilisateur | Oui | - |
| `PUT` | `/users/{id}` | MAJ utilisateur | Oui | `firstname`, `lastname`, `email`, `password` |
| `DELETE` | `/users/{id}` | Supprimer utilisateur | Oui | - |
| `GET` | `/users/{id}/vehicles` | Véhicules de l'user | Oui | - |
| `GET` | `/users/{id}/vehicles/count` | Nombre de véhicules | Oui | - |
| `GET` | `/users/{id}/maintenances` | Entretiens de l'user | Oui | - |
| `GET` | `/users/{id}/maintenances/future` | Entretiens futurs | Oui | Basé sur date ou kilométrage |
| `GET` | `/users/{id}/maintenances/late` | Entretiens en retard | Oui | Basé sur date ou kilométrage |
| `GET` | `/users/{id}/invoices` | Factures de l'user | Oui | - |

## Véhicules (Vehicules)

| Méthode | Endpoint | Description | Auth | Paramètres (Body) |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/vehicules` | Liste des véhicules | Oui | - |
| `POST` | `/vehicules` | Créer un véhicule | Oui | `name`, `brand`, `model`, `year`, `mileage`, `license_plate`, `user_id` |
| `GET` | `/vehicules/{id}` | Détails véhicule | Oui | - |
| `PUT` | `/vehicules/{id}` | MAJ véhicule | Oui | (Champs optionnels de création) |
| `DELETE` | `/vehicules/{id}` | Supprimer véhicule | Oui | - |
| `GET` | `/vehicules/{id}/maintenances` | Entretiens liés | Oui | - |
| `GET` | `/vehicules/{id}/invoices` | Factures liées | Oui | - |

## Entretiens (Maintenances)

| Méthode | Endpoint | Description | Auth | Paramètres (Body) |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/maintenances` | Liste entretiens | Oui | - |
| `POST` | `/maintenances` | Créer entretien | Oui | `type` (mileage/time/one_time), `description`, `scheduled_date`, `scheduled_mileage`, `vehicle_ids` (array) |
| `GET` | `/maintenances/{id}` | Détails entretien | Oui | - |
| `PUT` | `/maintenances/{id}` | MAJ entretien | Oui | (Champs optionnels + `done`, `done_date`, `done_mileage`, `cost`) |
| `DELETE` | `/maintenances/{id}` | Supprimer entretien | Oui | - |
| `GET` | `/maintenances/{id}/invoices` | Factures liées | Oui | - |

## Factures (Invoices)

| Méthode | Endpoint | Description | Auth | Paramètres (Body) |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/invoices` | Liste factures | Oui | - |
| `POST` | `/invoices` | Créer facture | Oui | `date`, `amount`, `description`, `file_path`, `vehicle_ids`, `maintenance_ids` |
| `GET` | `/invoices/{id}` | Détails facture | Oui | - |
| `PUT` | `/invoices/{id}` | MAJ facture | Oui | (Champs optionnels) |
| `DELETE` | `/invoices/{id}` | Supprimer facture | Oui | - |
