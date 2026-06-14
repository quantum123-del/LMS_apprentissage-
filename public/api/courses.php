<?php
require_once __DIR__ . '/../../api/helpers.php';

$action = $_GET['action'] ?? '';
if ($action === 'list') {
  listCourses();
} elseif ($action === 'detail') {
  getCourseDetail();
} elseif ($action === 'create') {
  createCourse();
} else {
  jsonError('Action invalide.');
}

function listCourses() {
  global $pdo;
  $stmt = $pdo->query(
    'SELECT c.id, c.title, c.description, u.name AS promoter_name, c.created_at FROM courses c JOIN users u ON c.promoter_id = u.id ORDER BY c.created_at DESC'
  );
  $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
  jsonSuccess('Liste des cours récupérée.', ['courses' => $courses]);
}

function getCourseDetail() {
  $courseId = intval($_GET['id'] ?? 0);
  if ($courseId <= 0) {
    jsonError('Identifiant de cours invalide.');
  }

  global $pdo;
  $stmt = $pdo->prepare(
    'SELECT c.id, c.title, c.description, u.name AS promoter_name, c.created_at FROM courses c JOIN users u ON c.promoter_id = u.id WHERE c.id = ?'
  );
  $stmt->execute([$courseId]);
  $course = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$course) {
    jsonError('Cours introuvable.');
  }

  $stmt = $pdo->prepare('SELECT m.id, m.title, m.description, m.`order` FROM modules m WHERE m.course_id = ? ORDER BY m.`order` ASC');
  $stmt->execute([$courseId]);
  $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

  jsonSuccess('Détail du cours récupéré.', ['course' => $course, 'modules' => $modules]);
}

function createCourse() {
  ensureRole('promoteur');

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode invalide.');
  }

  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');

  if ($title === '') {
    jsonError('Le titre du cours est requis.');
  }

  $user = getCurrentUser();
  global $pdo;
  $stmt = $pdo->prepare('INSERT INTO courses (promoter_id, title, description) VALUES (?, ?, ?)');
  $success = $stmt->execute([$user['id'], $title, $description]);

  if ($success) {
    jsonSuccess('Cours créé avec succès.', ['course_id' => $pdo->lastInsertId()]);
  }

  jsonError('Impossible de créer le cours.');
}
