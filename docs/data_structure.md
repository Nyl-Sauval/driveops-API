# Structure de Données (Data Structure)

Ce document décrit le modèle de données de l'API DriveOps, incluant les entités, leurs champs principaux et les relations entre elles.

## Entités

### User
Représente un utilisateur de l'application.

| Champ | Type | Description | Règles / Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Identifiant unique | PK, Auto-increment |
| `firstname` | String | Prénom | Requis |
| `lastname` | String | Nom de famille | Requis |
| `email` | String | Adresse email | Unique, Requis |
| `password` | String | Mot de passe haché | Requis (sauf si Google Auth), min 8 chars |
| `role` | String | Rôle de l'utilisateur | Default: 'user' |
| `avatar` | String | URL de l'avatar | Optionnel (via Google) |
| `google_id` | String | ID Google OAuth | Optionnel, Unique |
| `created_at` | Timestamp | Date de création | Auto |
| `updated_at` | Timestamp | Date de modification | Auto |

### Vehicule
Représente un véhicule appartenant à un utilisateur.

| Champ | Type | Description | Règles / Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Identifiant unique | PK, Auto-increment |
| `name` | String | Nom donné au véhicule | Requis, ex: "Ma Clio" |
| `brand` | String | Marque | Requis |
| `model` | String | Modèle | Requis |
| `year` | Year | Année de fabrication | Requis |
| `mileage` | Integer | Kilométrage actuel | Default: 0 |
| `license_plate` | String | Plaque d'immatriculation | Unique |
| `user_id` | BigInt | Propriétaire | FK -> users.id, Cascade Delete |
| `purchase_date` | Date | Date d'achat | Optionnel |
| `created_at` | Timestamp | Date de création | Auto |
| `updated_at` | Timestamp | Date de modification | Auto |

### Maintenance
Représente une opération de maintenance (passée ou future).

| Champ | Type | Description | Règles / Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Identifiant unique | PK, Auto-increment |
| `type` | Enum | Type de maintenance | Values: 'mileage', 'time', 'one_time' |
| `description` | Text | Détails de l'entretien | Optionnel |
| `scheduled_date` | Date | Date prévue | Optionnel |
| `scheduled_mileage` | Integer | Kilométrage prévu | Optionnel |
| `done` | Boolean | Statut réalisé | Default: false |
| `done_date` | Date | Date de réalisation | Optionnel |
| `done_mileage` | Integer | Kilométrage réel | Optionnel |
| `cost` | Decimal | Coût de l'entretien | Optionnel |
| `created_at` | Timestamp | Date de création | Auto |
| `updated_at` | Timestamp | Date de modification | Auto |

### Invoice
Représente une facture liée à des véhicules ou des maintenances.

| Champ | Type | Description | Règles / Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Identifiant unique | PK, Auto-increment |
| `date` | Date | Date facturation | Requis |
| `amount` | Decimal | Montant TTC | Requis |
| `description` | Text | Description | Optionnel |
| `file_path` | String | Chemin du fichier | Optionnel |
| `created_at` | Timestamp | Date de création | Auto |
| `updated_at` | Timestamp | Date de modification | Auto |

## Relations

### Diagramme Simplifié
- **User** 1 --- N **Vehicule**
- **Vehicule** N --- N **Maintenance**
- **Vehicule** N --- N **Invoice**
- **Maintenance** N --- N **Invoice**

### Détails des Relations

1. **User - Vehicule**
   - Type : **One-to-Many**
   - Clé étrangère : `vehicules.user_id`
   - Un utilisateur possède plusieurs véhicules.
   - Suppression en cascade : Si un User est supprimé, ses Vehicules le sont aussi.

2. **Vehicule - Maintenance**
   - Type : **Many-to-Many**
   - Table Pivot : `maintenance_vehicule`
   - Champs Pivot : `maintenance_id`, `vehicule_id`
   - Une maintenance peut concerner plusieurs véhicules (ex: contrôle technique de flotte - théorique, souvent 1-N en pratique mais modélisé N-N).
   - Un véhicule a plusieurs maintenances.

3. **Vehicule - Invoice**
   - Type : **Many-to-Many**
   - Table Pivot : `invoice_vehicule`
   - Champs Pivot : `invoice_id`, `vehicule_id`
   - Une facture peut être liée à plusieurs véhicules.

4. **Maintenance - Invoice**
   - Type : **Many-to-Many**
   - Table Pivot : `invoice_maintenance`
   - Champs Pivot : `invoice_id`, `maintenance_id`
   - Une facture peut couvrir plusieurs opérations de maintenance.

## Notes Spécifiques
- Les relations N-N (Many-to-Many) sont utilisées extensivement pour les liens entre Vehicule/Maintenance/Invoice, offrant une flexibilité maximale (ex: une facture globale pour plusieurs entretiens sur plusieurs véhicules), même si l'usage courant pourrait être plus simple (1 facture pour 1 entretien).
