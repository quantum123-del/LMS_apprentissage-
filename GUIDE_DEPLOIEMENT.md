# GUIDE DÉPLOIEMENT - LMS Platform sur Hébergement Gratuit

**Options gratuites recommandées**:
1. **000webhost** (`https://www.000webhost.com`) — PHP + MySQL + FTP
2. **InfinityFree** (`https://www.infinityfree.net`) — PHP + MySQL + cPanel

---

## OPTION A : 000webhost (Recommandé pour débuter)

### A1. Créer compte
1. Aller `https://www.000webhost.com`
2. Cliquer **"Sign Up Free"**
3. Remplir : Email, mot de passe, domaine (ex: `monlms.000webhostapp.com`)
4. Valider email
5. Accueil 000webhost s'affiche

### A2. Accéder File Manager
1. Dashboard → onglet **"File Manager"**
2. Voir dossier `public_html/` (racine web)

### A3. Télécharger fichiers du LMS
1. Local : zipper le dossier `lms_platform` :
```powershell
Compress-Archive -Path "C:\Users\LENOVO\LMS_Education\lms_platform" -DestinationPath "C:\Users\LENOVO\Desktop\lms_platform.zip"
```

2. Dans File Manager 000webhost :
   - Entrer dans `public_html/`
   - Cliquer **"Upload"**
   - Sélectionner `lms_platform.zip`
   - Attendre upload
   - Clic droit sur ZIP → **"Extract"**
   - Supprimer le ZIP après extraction

3. Résultat : `public_html/lms_platform/` (tous fichiers dedans)

### A4. Créer base de données
1. Dashboard 000webhost → **"Databases"**
2. Cliquer **"Create New Database"**
3. Nom : `lms_platform` (ou généré auto)
4. Username : `root_user` (ou auto)
5. Password : générer complexe
6. Cliquer **"Create"**
7. Copier les identifiants (hostname, username, password, db name)

### A5. Importer schema SQL
1. Dashboard → **"phpMyAdmin"** (lien accès à la BD)
2. Vous êtes connecté à votre BD
3. Cliquer onglet **"Import"**
4. Sélectionner fichier `lms_platform\sql\schema.sql` depuis votre ordi
5. Cliquer **"Go"**
6. Attendre "Import successful"
7. Vérifier : à gauche, 10 tables sont visibles

### A6. Mettre à jour config.php
1. File Manager → `public_html/lms_platform/api/config.php`
2. Cliquer **"Edit"** (icône crayon)
3. Remplacer contenu :
```php
<?php
define('DB_HOST', 'HOSTNAME_000WEBHOST');  // Ex: sql123.000webhost.com
define('DB_NAME', 'NOM_BD');               // Ex: id1234567_lms_platform
define('DB_USER', 'USERNAME');             // Ex: id1234567_root_user
define('DB_PASS', 'PASSWORD');             // Ex: abc123XYZ
```
4. Récupérer identifiants depuis l'email 000webhost ou dashboard Databases
5. Cliquer **"Save"**

### A7. Tester accès
- Navigateur : `http://monlms.000webhostapp.com/lms_platform/public/`
- Vous voyez accueil LMS
- ✅ Déploiement réussi

---

## OPTION A2 : Déploiement automatique via GitHub (recommandé)

Si vous voulez déployer plus rapidement et sans re-upload manuel, utilisez GitHub comme source et un workflow GitHub Actions pour envoyer automatiquement les fichiers à 000webhost.

### A2.1 Préparer le dépôt GitHub
1. Assurez-vous que le code est dans un repo GitHub.
2. Le repository doit contenir :
   - `public/` pour les fichiers web
   - `api/` pour la connexion MySQL
   - `sql/` pour le schéma
   - `.github/workflows/deploy-000webhost.yml`

### A2.2 Ajouter les secrets GitHub
1. Sur GitHub, ouvrez votre repo → **Settings** → **Secrets and variables** → **Actions**.
2. Ajouter ces secrets :
   - `FTP_HOST` (ex: `ftp.000webhost.com`)
   - `FTP_USERNAME`
   - `FTP_PASSWORD`
   - `FTP_PORT` (optionnel, par défaut `21`)

### A2.3 Explication du workflow
- À chaque push vers `main`, GitHub exécute un workflow
- Il envoie :
  - le contenu de `public/` vers `/public_html`
  - le contenu de `api/` vers `/public_html/api`

### A2.4 Déployer avec Git
1. Pousser vos modifications sur `main` :
```powershell
git add .
git commit -m "Deployment update"
git push origin main
```
2. GitHub lance automatiquement le déploiement.
3. Vérifiez l’état de l’action dans `Actions` sur GitHub.

### A2.5 Vérifier le site
- Ouvrez `http://monlms.000webhostapp.com/`
- Si le site ne s’affiche pas, vérifiez :
  - `api/config.php` sur 000webhost
  - la présence de `public_html/index.php`
  - les logs GitHub Action pour les erreurs FTP

---
## FINALISATION ET LIEN PUBLIC

### Lien final
- Si vous avez déployé le contenu dans `public_html/`, l’URL est :
  - `https://votresite.000webhostapp.com/`
- Si vous avez déployé dans `public_html/lms_platform/`, l’URL devient :
  - `https://votresite.000webhostapp.com/lms_platform/`

### Contrôles finaux
- Vérifiez que `api/config.php` est bien configuré
- Vérifiez que les dossiers `uploads/`, `uploads/pdfs/`, `uploads/videos/`, `uploads/certs/` existent en ligne
- Vérifiez que le workflow GitHub Action a été exécuté sans erreur
- Si le site est accessible, faites un test d’inscription et de login immédiatement

---
## OPTION B : InfinityFree (Alternative)

### B1. Créer compte
1. Aller `https://www.infinityfree.net`
2. Cliquer **"Get Started"**
3. Remplir : Email, mot de passe, domaine (ex: `monlms.infinityfreeapp.com`)
4. Valider email

### B2. Accéder cPanel
1. Dashboard → Cliquer **"Go to cPanel"**
2. Vous êtes dans l'interface cPanel d'InfinityFree

### B3. File Manager
1. cPanel → **"File Manager"**
2. Entrer dans `public_html/`

### B4. Upload et extraction
- Même processus que 000webhost (télécharger ZIP, uploader, extraire)

### B5. Créer BD (phpMyAdmin)
1. cPanel → **"MySQL® Databases"** ou **"phpMyAdmin"**
2. Créer BD `lms_platform`
3. Créer utilisateur + password
4. Donner tous privileges à l'utilisateur sur la BD

### B6. Importer schema
- Même processus : phpMyAdmin → Import → schema.sql

### B7. Configurer api/config.php
- Même processus : éditer avec identifiants InfinityFree

### B8. Tester
- Navigateur : `http://monlms.infinityfreeapp.com/lms_platform/public/`

---

## OPTION C : Déploiement via Git (Avancé)

Si vous avez un repo GitHub/GitLab :

### C1. Créer repo GitHub
1. GitHub.com → **"New Repository"**
2. Nom : `lms-platform`
3. Cloner localement :
```powershell
cd C:\Users\LENOVO\LMS_Education\lms_platform
git init
git add .
git commit -m "Initial LMS commit"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/lms-platform.git
git push -u origin main
```

### C2. Connecter hébergement à GitHub
- Certains hébergeurs (Heroku, Railway, Render) permettent déploiement direct depuis GitHub
- Alternative : pull via FTP/SSH sur serveur (avancé)

---

## VÉRIFICATIONS POST-DÉPLOIEMENT

### Checklist
- [ ] Site accessible via URL publique
- [ ] Page accueil charge (CSS + layout OK)
- [ ] Inscription fonctionne (AJAX)
- [ ] Login fonctionne (session créée)
- [ ] Upload PDF possible (si accès enseignant)
- [ ] BD queryable (phpMyAdmin accessible)
- [ ] Aucun erreur 500 en navigateur (F12 console)

### Si erreurs 500
1. Vérifier `api/config.php` identifiants
2. Vérifier BD schema.sql importé complètement
3. Vérifier chemins `uploads/` accessibles en écriture
4. Activer error reporting dans `api/db.php` temporairement :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## MAINTENANCE POST-DÉPLOIEMENT

### Sauvegardes
1. Télécharger régulièrement `lms_platform/public/uploads/` (fichiers utilisateurs)
2. Exporter BD via phpMyAdmin : **"Export"** → SQL format

### Mises à jour
1. Modifier fichier localement
2. Uploader via File Manager (overwrite)
3. Ou : recreate BD si schema change (supprimer + réimporter)

### Logs/Erreurs
- Vérifier `public/uploads/` pour fichiers (certificats, PDFs)
- Vérifier BD table `attempts` pour suivre étudiants

---

## LIMITATIONS HÉBERGEMENTS GRATUITS

⚠️ **000webhost / InfinityFree**:
- Storage limité (5-10 GB)
- Pas d'upload très volumineux de vidéos (ex: >100 MB)
- Peut être lent en heures de pointe
- Peuvent suspendre si peu d'activité

✅ **Solutions**:
- Streamer vidéos via YouTube/Vimeo plutôt que stocker localement
- Utiliser CDN externe pour fichiers volumineux
- Compresser PDFs avant upload

---

## ALTERNATIVE : Déploiement Local + Partage

Si vous voulez tester localement avant hébergement public:

### Partage via Ngrok (accès public temporaire)
1. Télécharger `ngrok` : `https://ngrok.com/download`
2. Lancer `ngrok http 80` (Apache sur port 80)
3. URL publique générée : ex `https://abc123.ngrok.io/lms_platform/public/`
4. Partageable temporairement (24h par défaut)

### Partage réseau local
- Autres appareils sur même WiFi : `http://YOUR_IP/lms_platform/public/`
- Trouver IP : `ipconfig` → IPv4 Address

---

## RÉSUMÉ DÉPLOIEMENT

| Plateforme | Coût | Setup | Perf | Recommandé |
|-----------|------|-------|------|-----------|
| 000webhost | ✅ Gratuit | 10 min | Moyen | ⭐⭐⭐ Débutants |
| InfinityFree | ✅ Gratuit | 10 min | Moyen | ⭐⭐⭐ Débutants |
| Ngrok | ✅ Gratuit | 2 min | ⭐⭐⭐⭐ Excellent | Test local |
| Heroku | ~$7/mois | 15 min | ⭐⭐⭐⭐ | Production |
| AWS EC2 | ~$5/mois | 30 min | ⭐⭐⭐⭐⭐ | Scalable |

---

## COMMANDES UTILES

### Vérifier connectivité DB en ligne
```php
<?php
// Créer un fichier test.php dans public/
require_once 'api/db.php';
require_once 'api/helpers.php';
echo "Connecté! Users: " . count($pdo->query('SELECT * FROM users')->fetchAll()) ?? 0;
```

### Voir logs erreurs
```powershell
# Local : Apache logs
Get-Content "C:\xampp\apache\logs\error.log" -Tail 20
```

### Reset BD (attention!)
```sql
-- SQL: supprimer BD et recommencer
DROP DATABASE lms_platform;
CREATE DATABASE lms_platform CHARACTER SET utf8mb4;
-- Puis réimporter schema.sql
```

---

**🚀 FIN GUIDE DÉPLOIEMENT**

Vous êtes maintenant prêt à :
1. Tester localement (XAMPP)
2. Déployer public (000webhost/InfinityFree)
3. Partager avec utilisateurs finaux

Besoin d'aide ? Consultez les autres docs (RAPPORT_VALIDATION.md, GUIDE_TEST_LOCAL.md, README.md)
