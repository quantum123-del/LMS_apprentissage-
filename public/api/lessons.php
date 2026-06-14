<?php
require_once __DIR__ . '/../../api/helpers.php';

$action = $_GET['action'] ?? '';
if ($action === 'create') {
  createLesson();
} elseif ($action === 'list') {
  listLessons();
} elseif ($action === 'detail') {
  getLessonDetail();
} else {
  jsonError('Action invalide.');
}

function createLesson() {
  ensureRole('enseignant');
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode invalide.');
  }

  $module_id = intval($_POST['module_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $type = $_POST['type'] ?? 'pdf';
  $file_path = trim($_POST['file_path'] ?? '') ?: null;
  $external_url = trim($_POST['external_url'] ?? '') ?: null;
  $order = intval($_POST['order'] ?? 0);

  if ($module_id <= 0 || $title === '') {
    jsonError('Module et titre requis.');
  }

  if ($type !== 'pdf' && $type !== 'video') {
    jsonError('Type de leçon invalide.');
  }

  global $pdo;
  $stmt = $pdo->prepare('INSERT INTO lessons (module_id, title, type, file_path, external_url, `order`) VALUES (?, ?, ?, ?, ?, ?)');
  $success = $stmt->execute([$module_id, $title, $type, $file_path, $external_url, $order]);

  if ($success) {
    jsonSuccess('Leçon créée.', ['lesson_id' => $pdo->lastInsertId()]);
  }
  jsonError('Impossible de créer la leçon.');
}

function listLessons() {
  $module_id = intval($_GET['module_id'] ?? 0);
  if ($module_id <= 0) {
    jsonError('Module invalide.');
  }
  global $pdo;
  $stmt = $pdo->prepare('SELECT * FROM lessons WHERE module_id = ? ORDER BY `order` ASC');
  $stmt->execute([$module_id]);
  $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
  jsonSuccess('Leçons récupérées.', ['lessons' => $lessons]);
}

function getLessonDetail() {
  $lesson_id = intval($_GET['id'] ?? 0);
  if ($lesson_id <= 0) jsonError('Leçon invalide.');
  global $pdo;
  $stmt = $pdo->prepare('SELECT * FROM lessons WHERE id = ?');
  $stmt->execute([$lesson_id]);
  $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$lesson) jsonError('Leçon introuvable.');
  jsonSuccess('Leçon récupérée.', ['lesson' => $lesson]);
}
