# Règles Métier (Business Rules)

Ce document centralise les règles logiques critiques de l'application DriveOps.

## 1. Entretiens et Alertes

### Règle : Définition des Entretiens Futurs
Un entretien est considéré comme "Futur" si au moins **UNE** des conditions suivantes est remplie :
1. **Date** : La `scheduled_date` est strictement supérieure à la date actuelle.
2. **Kilométrage** : Le `scheduled_mileage` est strictement supérieur au kilométrage actuel du véhicule lié. (Calculé via jointure sur la dernière valeur de `vehicules.mileage`).

### Règle : Définition des Entretiens en Retard
Un entretien est considéré comme "En Retard" si au moins **UNE** des conditions suivantes est remplie :
1. **Date** : La `scheduled_date` est strictement inférieure à la date actuelle.
2. **Kilométrage** : Le `scheduled_mileage` est inférieur ou égal au kilométrage actuel du véhicule lié.

*Note : La logique actuelle (implémentée via `orWhereExists` dans le Query Builder) applique un "OU" logique strict. Un entretien prévu pour une date future mais avec un kilométrage déjà dépassé apparaîtra dans les deux listes selon l'interprétation exacte des requêtes SQL.*

## 2. Véhicules et Propriété

### Règle : Unicité de la Plaque
- La plaque d'immatriculation (`license_plate`) doit être unique dans tout le système.
- Deux utilisateurs ne peuvent pas enregistrer le même véhicule (basé sur la plaque).

### Règle : Suppression en Cascade
- Supprimer un utilisateur supprime irréversiblement tous ses véhicules.
- Supprimer un véhicule supprime ses liens avec les entretiens et les factures (via `onDelete('cascade')` sur les tables pivots), mais ne supprime pas nécessairement les enregistrements d'entretien ou de facture eux-mêmes s'ils sont liés à d'autres entités (bien que ce cas soit rare fonctionnellement).

## 3. Facturation

### Règle : Liaisons Multiples
- Une facture (`Invoice`) n'est pas strictement liée à un utilisateur, mais transitoirement via les véhicules ou entretiens associés.
- Pour récupérer les factures d'un utilisateur, le système doit passer par ses véhicules : `User -> Vehicles -> Invoices`.

## 4. Sécurité et Accès

### Règle : Cloisonnement des Données (Tenancy)
- Un utilisateur (rôle 'user') ne doit voir QUE ses propres données.
- Implémenté via les `Policies` (UserPolicy, VehiculePolicy, etc.) qui vérifient systématiquement `model->user_id === user->id` ou les relations parentes.
