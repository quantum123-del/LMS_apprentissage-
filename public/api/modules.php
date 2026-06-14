<?php
require_once __DIR__ . '/../../api/helpers.php';

$action = $_GET['action'] ?? '';
if ($action === 'create') {
  createModule();
} elseif ($action === 'list') {
  listModules();
} else {
  jsonError('Action invalide.');
}

function createModule() {
  ensureRole('promoteur');
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode invalide.');
  }

  $course_id = intval($_POST['course_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $order = intval($_POST['order'] ?? 0);

  if ($course_id <= 0 || $title === '') {
    jsonError('Course et titre requis.');
  }

  global $pdo;
  $stmt = $pdo->prepare('INSERT INTO modules (course_id, title, description, `order`) VALUES (?, ?, ?, ?)');
  $success = $stmt->execute([$course_id, $title, $description, $order]);

  if ($success) {
    jsonSuccess('Module créé.', ['module_id' => $pdo->lastInsertId()]);
  }
  jsonError('Impossible de créer le module.');
}

function listModules() {
  $course_id = intval($_GET['course_id'] ?? 0);
  global $pdo;
  if ($course_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE course_id = ? ORDER BY `order` ASC');
    $stmt->execute([$course_id]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonSuccess('Modules récupérés.', ['modules' => $modules]);
  } else {
    $stmt = $pdo->query('SELECT * FROM modules ORDER BY id DESC');
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonSuccess('Modules récupérés.', ['modules' => $modules]);
  }
}
