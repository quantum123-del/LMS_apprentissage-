<?php
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur');
$userRole = htmlspecialchars($_SESSION['user_role'] ?? 'étudiant');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tableau de bord | LMS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">LMS</a>
      <nav>
        <span class="nav-text">Connecté en tant que <strong><?php echo $userRole; ?></strong></span>
        <a href="logout.php">Se déconnecter</a>
      </nav>
    </div>
  </header>

  <main class="container dashboard-page">
    <section class="dashboard-welcome">
      <h1>Bienvenue, <?php echo $userName; ?>.</h1>
      <p>Vous êtes connecté avec le rôle <strong><?php echo $userRole; ?></strong>. Le LMS vous permet de suivre un parcours d'apprentissage structuré.</p>
      <div class="dashboard-panels">
        <article class="panel">
          <h2>Progression</h2>
          <p>Visualiser vos leçons, quiz et certificats à venir.</p>
          <a class="button button-secondary" href="courses.php">Voir les cours</a>
        </article>
        <article class="panel">
          <h2>Modules</h2>
          <p>Les modules sont organisés par promoteur et validés à partir de 70 % de réussite.</p>
        </article>
        <article class="panel">
          <h2>Actions rapides</h2>
          <p>Accédez aux pages adaptées à votre rôle ou préparez un cours structuré.</p>
          <?php if ($userRole === 'promoteur'): ?>
            <a class="button button-primary" href="#">Créer un module</a>
          <?php elseif ($userRole === 'enseignant'): ?>
            <a class="button button-primary" href="#">Ajouter une leçon</a>
          <?php else: ?>
            <a class="button button-primary" href="courses.php">Suivre un cours</a>
          <?php endif; ?>
        </article>
      </div>
    </section>
  </main>
</body>
</html>
