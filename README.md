# ERP - Gestion Municipale Centralisée

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
    <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
    <img src="https://img.shields.io/badge/Security-AES--256--CBC-success?style=for-the-badge" alt="Security">
</p>

## À propos du projet

Cette application est un **ERP (Enterprise Resource Planning)** conçu pour centraliser, sécuriser et optimiser les processus techniques et administratifs d'une commune.

Le système remplace les flux de travail historiques basés sur des fichiers Excel disparates par une architecture relationnelle stricte, garantissant l'intégrité des données, la traçabilité des interventions et la sécurité des informations sensibles des citoyens.

## Fonctionnalités Principales

- ** Cartographie & Patrimoine :** Modélisation granulaire de l'infrastructure communale (Lieu-dit > Lieu Public > Bâtiment > Local Technique > Équipement).
- ** Gestion des Interventions :** Suivi complet des tickets (actions citoyennes et interventions techniques) avec assignation dynamique des ressources et rattachement budgétaire.
- ** Sécurité & RGPD :** Chiffrement de bout en bout (AES-256-CBC) des données financières et bancaires (IBAN, BIC) directement dans les modèles Eloquent. Masquage des données sensibles sur les interfaces front-end.
- ** Flux ETL Intégrés :** Commandes CLI personnalisées pour l'ingestion, le nettoyage (_Data Cleansing_) et la liaison intelligente des données historiques avec architecture de rollback sécurisée.

## Architecture & Technologies

- **Framework Backend :** Laravel (PHP)
- **Base de données :** PostgreSQL (Utilisation avancée des clés étrangères, contraintes d'intégrité, et typage spatial pour les parcelles).
- **Frontend :** Blade Templating couplé à Tailwind CSS pour une interface claire et réactive.
- **Architecture des données :** Conception en "entonnoir" pour lier les requêtes approximatives à des localisations physiques strictes.

## Installation & Déploiement

Pour configurer le projet en environnement de développement ou de production :

```bash
# 1. Cloner le dépôt
git clone [URL_DU_DEPOT]

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node.js (Frontend)
npm install && npm run build

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans le fichier .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=bdd_mairie
# ...

# 6. Exécuter les migrations (Structure de la BDD)
php artisan migrate

# 7. Lancer le serveur local
php artisan serve
```

Commandes Personnalisées (ETL)
Le projet intègre des scripts d'importation massifs pour migrer l'historique de la commune. Ces scripts gèrent le dédoublonnage, la détection des hiérarchies géographiques et la gestion des encodages.

Importer l'historique des actions et interventions :
php artisan import:historique storage/app/historique.csv

Annuler un import (Rollback de sécurité) :
Cette commande purge les tables enfants et tables pivots avant de supprimer les enregistrements racines pour garantir l'intégrité des clés étrangères.
php artisan import:historique storage/app/historique.csv --rollback

Lier le référentiel matériel (Compteurs) aux actions :
php artisan db:seed --class=CompteurEtlSeeder
**Guide d'évolution:**
Si vous devez maintenir ou faire évoluer ce code, voici les points d'entrée cruciaux :

Sécurité des données : Le chiffrement des données financières est géré nativement via les Mutateurs/Accesseurs Laravel. Ne modifiez pas le typage (TEXT) des colonnes chiffrées dans PostgreSQL.

Modèle Géographique : Si vous ajoutez de nouvelles entités spatiales, veillez à respecter la hiérarchie établie dans les formulaires de saisie (id_local > id_batiment > id_lieu > id_adresse).

Scripts ETL : Les algorithmes de croisement de données se trouvent dans app/Console/Commands/. Si les formats de fichiers Excel de la mairie changent, c'est ici qu'il faudra mettre à jour les index (mapping des colonnes).
