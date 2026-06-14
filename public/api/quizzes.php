<?php
require_once __DIR__ . '/../../api/helpers.php';

$action = $_GET['action'] ?? '';
if ($action === 'create') {
  createQuiz();
} elseif ($action === 'add_question') {
  addQuestion();
} elseif ($action === 'get') {
  getQuiz();
} else {
  jsonError('Action invalide.');
}

function createQuiz() {
  ensureRole('enseignant');
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Méthode invalide.');
  $lesson_id = intval($_POST['lesson_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $pass_score = intval($_POST['pass_score'] ?? 70);
  if ($lesson_id <= 0 || $title === '') jsonError('Lesson et title requis.');
  global $pdo;
  $stmt = $pdo->prepare('INSERT INTO quizzes (lesson_id, title, pass_score) VALUES (?, ?, ?)');
  $success = $stmt->execute([$lesson_id, $title, $pass_score]);
  if ($success) jsonSuccess('Quiz créé.', ['quiz_id' => $pdo->lastInsertId()]);
  jsonError('Impossible de créer le quiz.');
}

function addQuestion() {
  ensureRole('enseignant');
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Méthode invalide.');
  $quiz_id = intval($_POST['quiz_id'] ?? 0);
  $text = trim($_POST['text'] ?? '');
  $type = $_POST['type'] ?? 'mcq';
  $points = intval($_POST['points'] ?? 1);
  $choicesJson = $_POST['choices'] ?? '[]';
  $choices = json_decode($choicesJson, true);
  if ($quiz_id <= 0 || $text === '') jsonError('Quiz et texte requis.');
  global $pdo;
  $stmt = $pdo->prepare('INSERT INTO questions (quiz_id, text, type, points) VALUES (?, ?, ?, ?)');
  $stmt->execute([$quiz_id, $text, $type, $points]);
  $questionId = $pdo->lastInsertId();
  $stmtChoice = $pdo->prepare('INSERT INTO choices (question_id, text, is_correct) VALUES (?, ?, ?)');
  foreach ($choices as $c) {
    $stmtChoice->execute([$questionId, $c['text'] ?? '', $c['is_correct'] ? 1 : 0]);
  }
  jsonSuccess('Question ajoutée.', ['question_id' => $questionId]);
}

function getQuiz() {
  $lesson_id = intval($_GET['lesson_id'] ?? 0);
  if ($lesson_id <= 0) jsonError('Lesson invalide.');
  global $pdo;
  $stmt = $pdo->prepare('SELECT q.id, q.title, q.pass_score FROM quizzes q WHERE q.lesson_id = ?');
  $stmt->execute([$lesson_id]);
  $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$quiz) jsonError('Aucun quiz pour cette leçon.');
  $stmt = $pdo->prepare('SELECT id, text, type, points FROM questions WHERE quiz_id = ?');
  $stmt->execute([$quiz['id']]);
  $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($questions as &$q) {
    if ($q['type'] === 'mcq') {
      $stmt = $pdo->prepare('SELECT id, text FROM choices WHERE question_id = ?');
      $stmt->execute([$q['id']]);
      $q['choices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($q);
  }
  jsonSuccess('Quiz récupéré.', ['quiz' => $quiz, 'questions' => $questions]);
}
