<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
  header('Location: ../login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un quiz | LMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <main class="container auth-page">
    <section class="auth-card">
      <h1>Créer un quiz</h1>
      <div id="message"></div>
      <form id="quiz-form" onsubmit="createQuiz(event)">
        <label for="lesson_id">Lesson ID</label>
        <input type="number" id="lesson_id" name="lesson_id" required>

        <label for="title">Titre du quiz</label>
        <input type="text" id="title" name="title" required>

        <label for="pass_score">Seuil de validation (%)</label>
        <input type="number" id="pass_score" name="pass_score" value="70" min="0" max="100">

        <button class="button button-primary" type="submit">Créer le quiz</button>
      </form>

      <hr>
      <h2>Ajouter une question</h2>
      <form id="question-form" onsubmit="addQuestion(event)">
        <label for="quiz_id">Quiz ID</label>
        <input type="number" id="quiz_id" name="quiz_id" required>

        <label for="q_text">Texte de la question</label>
        <input type="text" id="q_text" name="q_text" required>

        <label>Choix (JSON format simple)</label>
        <textarea id="q_choices" placeholder='[{"text":"A","is_correct":false},{"text":"B","is_correct":true}]' rows="4"></textarea>

        <button class="button button-primary" type="submit">Ajouter la question</button>
      </form>
    </section>
  </main>

  <script>
    async function createQuiz(e) {
      e.preventDefault();
      const form = document.getElementById('quiz-form');
      const data = new FormData(form);
      const resp = await fetch('../api/quizzes.php?action=create', {method: 'POST', body: data});
      const res = await resp.json();
      const msg = document.getElementById('message');
      if (!res.success) { msg.textContent = res.message; msg.className='message error'; return; }
      msg.textContent = 'Quiz créé (ID: ' + res.quiz_id + ')'; msg.className='message success';
    }

    async function addQuestion(e) {
      e.preventDefault();
      const form = document.getElementById('question-form');
      const quiz_id = document.getElementById('quiz_id').value;
      const text = document.getElementById('q_text').value;
      let choices = [];
      try { choices = JSON.parse(document.getElementById('q_choices').value || '[]'); } catch (err) { alert('JSON invalide pour les choix'); return; }
      const fd = new FormData();
      fd.append('quiz_id', quiz_id);
      fd.append('text', text);
      fd.append('type', 'mcq');
      fd.append('points', 1);
      fd.append('choices', JSON.stringify(choices));
      const resp = await fetch('../api/quizzes.php?action=add_question', {method: 'POST', body: fd});
      const res = await resp.json();
      const msg = document.getElementById('message');
      if (!res.success) { msg.textContent = res.message; msg.className='message error'; return; }
      msg.textContent = 'Question ajoutée (ID: ' + res.question_id + ')'; msg.className='message success';
    }
  </script>
</body>
</html>
