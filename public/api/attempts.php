<?php
require_once __DIR__ . '/../../api/helpers.php';

// Submit an attempt: POST { quiz_id, answers: [{question_id, choice_id|null, text|null}, ...] }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  jsonError('Méthode invalide.');
}

ensureRole('etudiant');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
  // support form-encoded as fallback
  $quiz_id = intval($_POST['quiz_id'] ?? 0);
  $answers = isset($_POST['answers']) ? json_decode($_POST['answers'], true) : [];
} else {
  $quiz_id = intval($data['quiz_id'] ?? 0);
  $answers = $data['answers'] ?? [];
}

if ($quiz_id <= 0) {
  jsonError('Quiz invalide.');
}

$user = getCurrentUser();
global $pdo;

// Load questions for quiz
$stmt = $pdo->prepare('SELECT q.id, q.points FROM questions q WHERE q.quiz_id = ?');
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$questions) {
  jsonError('Pas de questions pour ce quiz.');
}

$max_score = 0;
foreach ($questions as $q) {
  $max_score += intval($q['points']);
}

$score = 0;
// Evaluate MCQ answers by comparing choices
foreach ($answers as $ans) {
  $qId = intval($ans['question_id'] ?? 0);
  $choiceId = isset($ans['choice_id']) ? intval($ans['choice_id']) : null;
  $text = $ans['text'] ?? null;

  if ($choiceId) {
    $stmt = $pdo->prepare('SELECT is_correct FROM choices WHERE id = ? AND question_id = ?');
    $stmt->execute([$choiceId, $qId]);
    $c = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($c && $c['is_correct']) {
      // add question points
      $stmt2 = $pdo->prepare('SELECT points FROM questions WHERE id = ?');
      $stmt2->execute([$qId]);
      $qrow = $stmt2->fetch(PDO::FETCH_ASSOC);
      $score += intval($qrow['points']);
    }
  } else {
    // For text answers, no auto scoring in MVP
  }
}

$passed = ($max_score > 0) ? ($score / $max_score * 100) >= 70 : false;

// Insert attempt
$stmt = $pdo->prepare('INSERT INTO attempts (user_id, quiz_id, score, max_score, passed) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$user['id'], $quiz_id, $score, $max_score, $passed ? 1 : 0]);
$attemptId = $pdo->lastInsertId();

// Insert answers
$insertAnswer = $pdo->prepare('INSERT INTO answers (attempt_id, question_id, choice_id, text_answer) VALUES (?, ?, ?, ?)');
foreach ($answers as $ans) {
  $qId = intval($ans['question_id'] ?? 0);
  $choiceId = isset($ans['choice_id']) ? intval($ans['choice_id']) : null;
  $text = $ans['text'] ?? null;
  $insertAnswer->execute([$attemptId, $qId, $choiceId, $text]);
}

// After attempt, compute module progress and issue certificate if validated
// Find lesson->module for this quiz
$stmt = $pdo->prepare('SELECT l.module_id FROM quizzes q JOIN lessons l ON q.lesson_id = l.id WHERE q.id = ?');
$stmt->execute([$quiz_id]);
$lessonRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($lessonRow) {
  $moduleId = intval($lessonRow['module_id']);
  // compute progress across lessons in module
  $stmt = $pdo->prepare('SELECT id FROM lessons WHERE module_id = ?');
  $stmt->execute([$moduleId]);
  $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $lessonScores = [];
  foreach ($lessons as $ls) {
    $lId = intval($ls['id']);
    // find quiz for lesson
    $stmtQ = $pdo->prepare('SELECT id FROM quizzes WHERE lesson_id = ?');
    $stmtQ->execute([$lId]);
    $q = $stmtQ->fetch(PDO::FETCH_ASSOC);
    if (!$q) continue;
    $qId = intval($q['id']);
    // user best attempt for that quiz
    $stmtA = $pdo->prepare('SELECT MAX(score) AS best_score, MAX(max_score) AS best_max FROM attempts WHERE user_id = ? AND quiz_id = ?');
    $stmtA->execute([$user['id'], $qId]);
    $best = $stmtA->fetch(PDO::FETCH_ASSOC);
    if ($best && $best['best_max'] > 0) {
      $lessonScores[] = ($best['best_score'] / $best['best_max']) * 100;
    }
  }

  $progress = 0;
  if (count($lessonScores) > 0) {
    $progress = array_sum($lessonScores) / count($lessonScores);
  }

  // If progress >= 70 and certificate not yet issued
  if ($progress >= 70) {
    // check existing certificate
    $stmtC = $pdo->prepare('SELECT id FROM certificates WHERE user_id = ? AND module_id = ?');
    $stmtC->execute([$user['id'], $moduleId]);
    if (!$stmtC->fetch()) {
      // generate certificate image (PNG) using GD
      $certDir = __DIR__ . '/../../public/uploads/certs/';
      if (!is_dir($certDir)) @mkdir($certDir, 0755, true);
      $filename = 'cert_' . $user['id'] . '_' . $moduleId . '_' . time() . '.png';
      $path = $certDir . $filename;

      $imgW = 1200; $imgH = 800;
      $img = imagecreatetruecolor($imgW, $imgH);
      $bg = imagecolorallocate($img, 255, 255, 255);
      $titleColor = imagecolorallocate($img, 45, 127, 249);
      $textColor = imagecolorallocate($img, 20, 20, 20);
      imagefilledrectangle($img, 0, 0, $imgW, $imgH, $bg);

      // Title
      $title = "Certificat de validation";
      imagestring($img, 5, 40, 40, $title, $titleColor);

      $nameLine = "Attribué à: " . ($user['name'] ?? '');
      imagestring($img, 4, 40, 140, $nameLine, $textColor);

      $moduleLine = "Module ID: " . $moduleId;
      imagestring($img, 4, 40, 200, $moduleLine, $textColor);

      $dateLine = "Date: " . date('Y-m-d H:i:s');
      imagestring($img, 3, 40, 260, $dateLine, $textColor);

      imagepng($img, $path);
      imagedestroy($img);

      $webPath = 'uploads/certs/' . $filename;
      $stmtIns = $pdo->prepare('INSERT INTO certificates (user_id, module_id, file_path) VALUES (?, ?, ?)');
      $stmtIns->execute([$user['id'], $moduleId, $webPath]);
    }
  }
}

jsonSuccess('Tentative enregistrée.', ['score' => $score, 'max_score' => $max_score, 'passed' => $passed, 'progress' => $progress]);
