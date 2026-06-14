# LMS Platform

Plateforme LMS locale construite avec HTML, CSS, JavaScript, AJAX, PHP et MySQL.

## Description

Cette application propose une architecture simple pour un LMS avec 3 rôles :
- `promoteur` : gestion des modules et certificats.
- `enseignant` : création de cours, leçons PDF/vidéo et évaluations.
- `étudiant` : suivi de leçons, passage d'évaluations, progression et certification.

## Structure du projet

- `public/` : dossier accessible par le serveur web.
- `public/assets/` : CSS et JavaScript frontend.
- `public/api/` : endpoints PHP consommés par AJAX.
- `public/uploads/` : stocke les PDFs, vidéos et certificats.
- `api/` : configuration et connexion MySQL sécurisée.
- `sql/schema.sql` : script de création de base de données.

## Installation locale

1. Installer XAMPP, WAMP ou MAMP.
2. Copier `lms_platform` dans le dossier web (`htdocs` ou `www`).
3. Créer la base de données MySQL avec `sql/schema.sql`.
4. Mettre à jour `api/config.php` avec les identifiants MySQL.
5. Lancer le serveur Apache et visiter `http://localhost/lms_platform/public/`.
