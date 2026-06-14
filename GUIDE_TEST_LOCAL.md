# GUIDE TEST LOCAL - LMS Platform (XAMPP/Windows)

**Durée estimée**: 15-20 minutes  
**Prérequis**: XAMPP installé avec Apache + MySQL + PHP

---

## ÉTAPE 1 : Vérifier XAMPP et copier le projet

### 1.1 Démarrer XAMPP
- Ouvrir **XAMPP Control Panel** (`C:\xampp\xampp-control.exe`)
- Cliquer sur **"Start"** pour **Apache** et **MySQL**
- Attendre que les deux affichent "Running" (ports 80, 3306)

### 1.2 Copier le projet
```powershell
# Copier le dossier lms_platform dans htdocs de XAMPP
Copy-Item -Path "C:\Users\LENOVO\LMS_Education\lms_platform" -Destination "C:\xampp\htdocs\" -Recurse
# Vérifier
Test-Path C:\xampp\htdocs\lms_platform
# Résultat attendu: True
```

---

## ÉTAPE 2 : Créer la base de données

### 2.1 Accéder à phpMyAdmin
- Ouvrir navigateur : `http://localhost/phpmyadmin`
- Login : user = `root`, password = (laisser vide pour XAMPP par défaut)

### 2.2 Importer schema.sql
1. Cliquer onglet **"Import"** (haut de page)
2. Cliquer **"Choose File"** et sélectionner : `C:\xampp\htdocs\lms_platform\sql\schema.sql`
3. Cliquer **"Go"**
4. Attendre le message "Import has been successfully completed"
5. Vérifier : à gauche, vous voyez **"lms_platform"** dans la liste des BD

### 2.3 Vérifier les tables (optionnel)
- Cliquer sur **"lms_platform"** → vous voyez 10 tables (users, courses, modules, lessons, quizzes, questions, choices, attempts, answers, enrollments, certificates)

---

## ÉTAPE 3 : Vérifier et configurer la connexion DB

### 3.1 Vérifier api/config.php
- Fichier : `C:\xampp\htdocs\lms_platform\api\config.php`
- Contenu doit être :
```php
<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'lms_platform');
define('DB_USER', 'root');
define('DB_PASS', '');  // Vide pour XAMPP par défaut
```
- ✅ C'est déjà correct par défaut

---

## ÉTAPE 4 : Tester l'accès au LMS

### 4.1 Ouvrir l'accueil
- Navigateur : `http://localhost/lms_platform/public/`
- Vous voyez : page accueil avec titre "LMS Plateforme", boutons "Commencer" et "Se connecter"
- ✅ **TEST 1 RÉUSSI**

### 4.2 Tester l'inscription
1. Cliquer **"S'inscrire"** ou visiter `http://localhost/lms_platform/public/register.php`
2. Remplir formulaire :
   - Nom : `Etudiant Test`
   - Email : `etudiant@test.com`
   - Password : `password123`
3. Cliquer **"S'inscrire"**
4. Attendre message vert "Inscription réussie"
5. Vous êtes redirigé vers login après 1.2 sec
6. ✅ **TEST 2 RÉUSSI** (AJAX login form works)

### 4.3 Créer un utilisateur enseignant (SQL)
**Note**: L'inscription web crée par défaut `etudiant`. Pour créer enseignant/promoteur, utiliser SQL :

```powershell
# Depuis PowerShell, se connecter à MySQL
C:\xampp\mysql\bin\mysql.exe -u root -p lms_platform
# (appuyer Entrée pour password vide)
```

Dans MySQL CLI, exécuter :
```sql
-- Voir les utilisateurs créés
SELECT id, name, email, role FROM users;

-- Créer enseignant (remplacer ID par celui de l'utilisateur à modifier)
UPDATE users SET role='enseignant' WHERE id=1;

-- Créer promoteur
INSERT INTO users (name, email, password_hash, role) VALUES ('Promoteur Test', 'promoteur@test.com', '$2y$10$abcdef...', 'promoteur');
-- OU modifier un existant
UPDATE users SET role='promoteur' WHERE id=2;

-- Quitter
EXIT;
```

**Ou via phpMyAdmin** :
1. Sélectionner BD `lms_platform`
2. Table `users` → cliquer sur user → Edit
3. Changer `role` de `etudiant` à `enseignant` ou `promoteur`
4. Cliquer `Go`

---

## ÉTAPE 5 : Test flux PROMOTEUR

### 5.1 Créer un cours
1. Se connecter : `http://localhost/lms_platform/public/login.php`
   - Email: `promoteur@test.com`
   - Password: `password123`
2. Dashboard s'affiche (rôle = "promoteur")
3. Aller à `http://localhost/lms_platform/public/teacher/create_module.php`
4. Remplir formulaire "Créer un module" :
   - Course ID : `1` (ou chercher via SQL: `SELECT id FROM courses;`)
   - Titre : `Module Mathématiques Basique`
   - Description : `Apprentissage des bases mathématiques`
5. Cliquer **"Créer"**
6. Message vert : "Module créé (ID: X)"
7. ✅ **TEST 3 RÉUSSI** (Module creation + AJAX)

**Si Course n'existe pas**, créer via SQL :
```sql
INSERT INTO courses (promoter_id, title, description) VALUES (PROMOTER_ID, 'Mon Cours', 'Desc');
-- Remplacer PROMOTER_ID par id du promoteur (ex: 2)
```

---

## ÉTAPE 6 : Test flux ENSEIGNANT

### 6.1 Créer une leçon avec PDF
1. Se connecter en tant qu'enseignant : `http://localhost/lms_platform/public/login.php`
2. Aller à `http://localhost/lms_platform/public/teacher/create_lesson.php`
3. Remplir :
   - Module ID : `1` (créé à l'étape 5)
   - Titre : `Leçon 1 : Addition et Soustraction`
   - Type : choisir **"PDF"**
   - Fichier : sélectionner un fichier PDF local (ou créer un dummy PDF de test)
4. Cliquer **"Créer la leçon"**
5. Message vert : "Leçon créée (ID: X)"
6. Vérifier fichier uploadé : `C:\xampp\htdocs\lms_platform\public\uploads\pdfs\`
7. ✅ **TEST 4 RÉUSSI** (Upload PDF + lesson creation + AJAX)

### 6.2 Créer un quiz avec questions
1. Aller à `http://localhost/lms_platform/public/teacher/create_quiz.php`
2. **Partie 1 - Créer quiz** :
   - Lesson ID : `1` (créé ci-dessus)
   - Titre : `Quiz Leçon 1 : Addition`
   - Seuil de validation : `70`
   - Cliquer **"Créer le quiz"**
   - Message : "Quiz créé (ID: 1)"

3. **Partie 2 - Ajouter question** :
   - Quiz ID : `1` (récupéré ci-dessus)
   - Texte : `Quel est le résultat de 5 + 3 ?`
   - Choix (JSON) : copier/coller ceci
   ```json
   [{"text":"7","is_correct":false},{"text":"8","is_correct":true},{"text":"9","is_correct":false},{"text":"10","is_correct":false}]
   ```
   - Cliquer **"Ajouter la question"**
   - Message : "Question ajoutée (ID: X)"

4. Ajouter 2-3 autres questions de même format
5. ✅ **TEST 5 RÉUSSI** (Quiz creation + Questions + AJAX JSON)

---

## ÉTAPE 7 : Test flux ÉTUDIANT

### 7.1 Consulter catalogue de cours
1. Se connecter en tant qu'étudiant : email `etudiant@test.com`, password `password123`
2. Cliquer **"Voir les cours"** ou aller `http://localhost/lms_platform/public/courses.php`
3. Page affiche les cours disponibles (AJAX chargement)
4. ✅ **TEST 6 RÉUSSI** (Course listing via AJAX)

### 7.2 Ouvrir un cours et consulter ses modules
1. Cliquer sur un cours → page `course.php?id=1`
2. Affiche titre, description, liste modules (AJAX chargement)
3. ✅ **TEST 7 RÉUSSI** (Course detail via AJAX)

### 7.3 Voir une leçon
1. Depuis la page course, trouver le module et cliquer **"Voir les leçons"** (ou naviguer vers `public/student/lesson.php?id=1`)
2. Affiche : titre leçon + lecteur PDF (ou vidéo)
3. Ci-dessous : quiz chargé dynamiquement (AJAX)
4. ✅ **TEST 8 RÉUSSI** (Lesson view + PDF embed + AJAX)

### 7.4 Passer le quiz
1. Sur la page leçon, le quiz s'affiche avec questions et choix (radio buttons)
2. Sélectionner les bonnes réponses :
   - Q1 : sélectionner "8"
   - Q2, Q3, etc : répondre
3. Cliquer **"Soumettre"**
4. Résultat affiche :
   - Score (ex: "6/6")
   - Progression module (ex: "100%")
   - Message "Validé" ou "Non validé"
5. Si progression >= 70% : message "Certificat généré"
6. ✅ **TEST 9 RÉUSSI** (Quiz submission + scoring + progress + certificate generation)

### 7.5 Vérifier certificat généré
- Fichier généré dans : `C:\xampp\htdocs\lms_platform\public\uploads\certs\cert_USERID_MODULEID_TIMESTAMP.png`
- Ouvrir le fichier PNG : affiche certificat basique avec nom étudiant + module + date
- ✅ **TEST 10 RÉUSSI** (Certificate PNG generation)

---

## ÉTAPE 8 : Vérifier la base de données

### 8.1 Vérifier les tentatives (attempts)
- phpMyAdmin → `lms_platform` → `attempts`
- Vous voyez une ligne :
  - `user_id` = ID étudiant
  - `quiz_id` = ID quiz
  - `score` = 6 (ex: réponses correctes)
  - `max_score` = 6
  - `passed` = 1 (true)
  - `created_at` = timestamp actuel

### 8.2 Vérifier les certificats (certificates)
- phpMyAdmin → `lms_platform` → `certificates`
- Vous voyez une ligne :
  - `user_id` = ID étudiant
  - `module_id` = ID module
  - `file_path` = `uploads/certs/cert_...png`
  - `issued_at` = timestamp actuel

### 8.3 Vérifier les réponses (answers)
- `answers` table contient les réponses soumises
- Jointe à `attempt_id` et `question_id`

---

## ÉTAPE 9 : Test multiple tentatives

### 9.1 Repasser le quiz
1. Sur même page leçon, cliquer **"Soumettre"** de nouveau
2. Répondre différemment (ex: seulement 5/6 correctes)
3. Cliquer **"Soumettre"**
4. Résultat : score plus bas (5/6), progression recalculée
5. DB : nouvelle ligne dans `attempts` table

### 9.2 Vérifier meilleure tentative
- Étudiant a 2 tentatives : 6/6 et 5/6
- Progression module = meilleure = (6/6) × 100% = 100%
- **LE SCORE LE PLUS ÉLEVÉ EST CONSERVÉ** ✅

---

## ÉTAPE 10 : Test AJAX en détail (navigateur)

### 10.1 Ouvrir DevTools
- Appuyer **F12** dans navigateur
- Onglet **"Network"**

### 10.2 Refaire un test étudiant
1. Aller `courses.php`
2. Dans Network, filtrer par **"Fetch/XHR"** ou tout
3. Vous voyez requête `api/courses.php?action=list` (GET)
4. Response: JSON avec liste courses

5. Submetre quiz
6. Vous voyez requête `api/attempts.php` (POST) avec body JSON
7. Response: `{success: true, score: 6, passed: true, progress: 100}`

8. ✅ **AJAX confirmé** : fetch() utilisé partout, zéro rechargement page

---

## RÉSUMÉ DES TESTS (Checklist)

- [ ] **Test 1**: Accueil LMS chargé (`http://localhost/lms_platform/public/`)
- [ ] **Test 2**: Inscription utilisateur (AJAX login)
- [ ] **Test 3**: Création module (promoteur)
- [ ] **Test 4**: Upload PDF leçon (enseignant)
- [ ] **Test 5**: Création quiz + questions (enseignant)
- [ ] **Test 6**: Listing courses (étudiant, AJAX)
- [ ] **Test 7**: Détail course + modules (étudiant, AJAX)
- [ ] **Test 8**: View leçon + PDF (étudiant, AJAX)
- [ ] **Test 9**: Passer quiz + score + certificat (étudiant, AJAX)
- [ ] **Test 10**: Vérifier certificat PNG généré

**Si tous les tests passent → ✅ LMS ENTIÈREMENT FONCTIONNEL**

---

## TROUBLESHOOTING

### Erreur : "Impossible de contacter le serveur"
- Vérifier Apache+MySQL running dans XAMPP
- Vérifier `http://localhost/` accessible
- Vérifier fichiers copiés dans `C:\xampp\htdocs\lms_platform\`

### Erreur : "Erreur de connexion à la base de données"
- Vérifier MySQL running
- Vérifier `api/config.php` avec bons identifiants (root, sans password)
- Vérifier BD `lms_platform` existe (via phpMyAdmin)
- Vérifier `sql/schema.sql` importé

### Upload fichier échoue
- Vérifier permissions `C:\xampp\htdocs\lms_platform\public\uploads\` (accessible en écriture)
- Vérifier `php.ini` : `upload_max_filesize = 50M`
- Redémarrer Apache via XAMPP

### Quiz ne s'affiche pas
- Vérifier leçon a un quiz associé (table `quizzes`)
- Vérifier quiz a des questions (table `questions`)
- Vérifier navigateur console (F12) pour erreurs JS

### CSS/JS ne charge pas
- Vérifier chemin assets : `public/assets/css/style.css` et `public/assets/js/*.js`
- Vérifier permissions lecture
- Vérifier URL : `http://localhost/lms_platform/public/` (pas sans `/public/`)

---

## FICHIERS DE SORTIE ATTENDUS

Après tous les tests, vous devez avoir:

```
C:\xampp\htdocs\lms_platform\
├── public/
│   ├── uploads/
│   │   ├── pdfs/
│   │   │   └── XXXXXXX_YYYYY.pdf     # Fichier PDF uploadé
│   │   ├── videos/
│   │   ├── certs/
│   │   │   └── cert_1_1_1234567890.png   # Certificat généré
```

BD `lms_platform` contient:
- Table `users` : ≥2 rows (étudiant + enseignant/promoteur)
- Table `courses` : ≥1 row
- Table `modules` : ≥1 row
- Table `lessons` : ≥1 row (avec file_path pointant vers uploads/pdfs/)
- Table `quizzes` : ≥1 row
- Table `questions` : ≥3 rows
- Table `choices` : ≥8 rows (2-3 choix par question)
- Table `attempts` : ≥2 rows (multiple tentatives)
- Table `answers` : ≥6 rows (réponses par tentative)
- Table `certificates` : ≥1 row (si module progressé ≥70%)

---

**🎉 RÉSULTAT FINAL**: Si tous les tests passent, votre LMS est **100% OPÉRATIONNEL LOCALEMENT**

Prochaine étape : déploiement sur 000webhost/InfinityFree (voir guide distinct)
