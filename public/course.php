<?php
session_start();
$courseId = intval($_GET['id'] ?? 0);
if ($courseId <= 0) {
  header('Location: courses.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Détail du cours | LMS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">LMS</a>
      <nav>
        <a href="courses.php">Retour aux cours</a>
        <a href="dashboard.php">Tableau de bord</a>
      </nav>
    </div>
  </header>

  <main class="container auth-page">
    <section class="auth-card">
      <h1 id="course-title">Chargement...</h1>
      <p id="course-description"></p>
      <div id="module-list" class="features-grid"></div>
    </section>
  </main>

  <script>
    const COURSE_ID = <?php echo $courseId; ?>;
  </script>
  <script src="assets/js/course.js"></script>
</body>
</html>
