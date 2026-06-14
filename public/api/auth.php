<?php
require_once __DIR__ . '/../../api/helpers.php';

action();

function action() {
  $action = $_GET['action'] ?? '';
  if ($action === 'register') {
    register();
  } elseif ($action === 'login') {
    login();
  } elseif ($action === 'logout') {
    logout();
  } else {
    jsonError('Action invalide.');
  }
}

function register() {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode invalide.');
  }

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($name === '' || $email === '' || $password === '') {
    jsonError('Tous les champs sont requis.');
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Email invalide.');
  }

  global $pdo;
  $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    jsonError('Cet email est déjà utilisé.');
  }

  $passwordHash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
  $success = $stmt->execute([$name, $email, $passwordHash, 'etudiant']);

  if ($success) {
    jsonSuccess('Inscription réussie. Vous pouvez vous connecter.');
  }

  jsonError('Impossible de créer le compte.');
}

function login() {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode invalide.');
  }

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    jsonError('Email et mot de passe requis.');
  }

  global $pdo;
  $stmt = $pdo->prepare('SELECT id, name, password_hash, role FROM users WHERE email = ?');
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($password, $user['password_hash'])) {
    jsonError('Email ou mot de passe incorrect.');
  }

  $_SESSION['user_id'] = $user['id'];
  $_SESSION['user_name'] = $user['name'];
  $_SESSION['user_role'] = $user['role'];

  jsonSuccess('Connexion réussie.', ['role' => $user['role']]);
}

function logout() {
  session_unset();
  session_destroy();
  jsonSuccess('Déconnecté.');
}
