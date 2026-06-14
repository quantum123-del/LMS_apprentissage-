<?php
session_start();
$userRole = $_SESSION['user_role'] ?? null;
if (empty($_SESSION['user_id']) || $userRole !== 'etudiant') {
  header('Location: ../login.php');
  exit;
}
$lessonId = intval($_GET['id'] ?? 0);
if ($lessonId <= 0) {
  header('Location: ../courses.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leçon | LMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <main class="container auth-page">
    <section class="auth-card">
      <h1 id="lesson-title">Chargement...</h1>
      <div id="lesson-content"></div>
      <div id="quiz-area"></div>
    </section>
  </main>
  <script>
    const LESSON_ID = <?php echo $lessonId; ?>;
  </script>
  <script src="../assets/js/quizzes.js"></script>
  <script src="../assets/js/lesson_view.js"></script>
</body>
</html>
