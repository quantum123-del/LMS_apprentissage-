<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function getCurrentUser() {
  global $pdo;
  if (empty($_SESSION['user_id'])) {
    return null;
  }

  $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function ensureLoggedIn() {
  if (getCurrentUser() === null) {
    header('Location: login.php');
    exit;
  }
}

function ensureRole($roles) {
  if (is_string($roles)) {
    $roles = [$roles];
  }

  $user = getCurrentUser();
  if ($user === null || !in_array($user['role'], $roles, true)) {
    header('Location: dashboard.php');
    exit;
  }
}

function jsonResponse($success, $message, $data = []) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
  exit;
}

function jsonError($message) {
  jsonResponse(false, $message);
}

function jsonSuccess($message, $data = []) {
  jsonResponse(true, $message, $data);
}
