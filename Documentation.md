TickMaster - Documentation Utilisateur & Développeur

1. Introduction

TickMaster est une application de gestion de tickets développée avec Symfony 7.2 et PHP 8.4. Elle permet de :

Créer, modifier et supprimer des tickets avec workflow de statut

Consulter un tableau de bord personnalisable avec graphiques

Filtrer, trier et paginer la liste des tickets

Ajouter des commentaires et pièces jointes

Suivre l’historique des changements de statut

Gérer les droits d’accès selon les rôles (User, Technicien, Admin)

2. Prérequis

PHP 8.4 avec extension fileinfo activée

Composer 2.x

Node.js ≥ 16 et npm

MySQL (ou autre SGBD compatible Doctrine)

Docker (optionnel pour la BDD)

3. Installation

3.1. Cloner et installer les dépendances

git clone <repo_url> tickmaster
cd tickmaster
composer install
npm install

3.2. Configuration

Copier .env en .env.local et renseigner la BDD et APP_SECRET.

Ajouter dans config/services.yaml :

parameters:
    attachments_directory: '%kernel.project_dir%/public/uploads/attachments'

Créer le dossier des uploads :

mkdir public\\uploads\\attachments
icacls public\\uploads\\attachments /grant Everyone:(OI)(CI)M

3.3. Base de données & fixtures

php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --append

3.4. Compilation des assets

npm run dev  # ou npx encore dev

Assurez-vous d’inclure dans templates/base.html.twig :

{{ encore_entry_link_tags('app') }}
{{ encore_entry_script_tags('app') }}

4. Fonctionnalités Principales

4.1. Tableau de bord

Cartes de statistiques (tickets par statut)

Graphiques (bar chart + pie chart)

Drag & drop pour réorganiser et mémoriser l’ordre

Thème personnalisé et affichage conditionnel des graphiques

4.2. Gestion des Tickets

CRUD complet avec formulaire Bootstrap 5

Workflow de statut (open → in_progress → resolved → closed)

Sécurité par voter (EDIT, DELETE selon rôle)

4.3. Filtrage & Tri

Formulaire en ligne pour statut, priorité, tri, direction

Offcanvas pour filtres avancés (dates création/mise à jour)

Pagination Doctrine intégrée

4.4. Commentaires & Pièces jointes

Formulaire de commentaire avec zone de texte

Upload multiple de fichiers (PDF, images, etc.)

Affichage et suppression des pièces jointes

4.5. Historique de statuts

Enregistrement en base de chaque transition de statut

Affichage chronologique dans la colonne droite de la vue ticket

5. Personnalisation & Thèmes

Thème clair/sombre et couleurs de graphiques configurables par utilisateur

Layout du dashboard enregistré et restauré à chaque connexion

6. Administration & Rôles

Rôle

Création

Lecture

Modification

Suppression

ROLE_USER

✔

✔

✖ (sur ses propres tickets)

✖

ROLE_TECHNICIEN

✔

✔

✔

✖

ROLE_ADMIN

✔

✔

✔

✔

7. Développement

Code sous src/ (Controller, Entity, Repository, Form)

Templates Twig dans templates/

Workflows configurés via config/packages/workflow.yaml

Services et paramètres dans config/services.yaml

8. Bonnes pratiques

Respecter les Voter pour toute nouvelle entité sécurisée

Ajouter les migrations après toute modification d’entités

Nettoyer le cache (php bin/console cache:clear) après configuration

Versionner les assets compilés (ou les ignorer si CDN)

9. Support & Évolution

Pour toute question ou demande d’évolution (Kanban, notifications temps réel, rapports PDF…), ouvre une issue sur le dépôt ou contacte l’équipe de développement.

© 2025 TickMaster. Tous droits réservés.

