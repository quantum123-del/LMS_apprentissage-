<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cours | LMS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">LMS</a>
      <nav>
        <a href="dashboard.php">Tableau de bord</a>
        <a href="logout.php">Déconnexion</a>
      </nav>
    </div>
  </header>

  <main class="container auth-page">
    <section class="auth-card">
      <h1>Catalogue des cours</h1>
      <p>Explore les cours disponibles et commence ton parcours d'apprentissage.</p>
      <div id="courses-list" class="features-grid"></div>
    </section>
  </main>

  <script src="assets/js/courses.js"></script>
</body>
</html>
