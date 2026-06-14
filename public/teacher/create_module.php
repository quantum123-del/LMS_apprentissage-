<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'promoteur') {
  header('Location: ../login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un module | LMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <main class="container auth-page">
    <section class="auth-card">
      <h1>Créer un module</h1>
      <div id="message"></div>
      <form id="module-form" onsubmit="createModule(event)">
        <label for="course_id">Course ID</label>
        <input type="number" id="course_id" name="course_id" required>

        <label for="title">Titre du module</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description</label>
        <textarea id="description" name="description"></textarea>

        <button class="button button-primary" type="submit">Créer</button>
      </form>
    </section>
  </main>
  <script>
    async function createModule(e) {
      e.preventDefault();
      const form = document.getElementById('module-form');
      const data = new FormData(form);
      const resp = await fetch('../api/modules.php?action=create', {method: 'POST', body: data});
      const res = await resp.json();
      const msg = document.getElementById('message');
      if (!res.success) { msg.textContent = res.message; msg.className='message error'; return; }
      msg.textContent = 'Module créé (ID: ' + res.module_id + ')'; msg.className='message success';
    }
  </script>
</body>
</html>
