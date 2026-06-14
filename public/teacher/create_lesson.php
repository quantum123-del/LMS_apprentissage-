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
  <title>Créer une leçon | LMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <main class="container auth-page">
    <section class="auth-card">
      <h1>Créer une leçon</h1>
      <div id="message"></div>
      <form id="lesson-form" onsubmit="createLesson(event)">
        <label for="module_id">Module ID</label>
        <input type="number" id="module_id" name="module_id" required>

        <label for="title">Titre de la leçon</label>
        <input type="text" id="title" name="title" required>

        <label for="type">Type</label>
        <select id="type" name="type">
          <option value="pdf">PDF</option>
          <option value="video">Vidéo</option>
        </select>

        <label for="file">Fichier (PDF ou MP4)</label>
        <input type="file" id="file" name="file" accept="application/pdf,video/mp4">

        <label for="external_url">URL externe (optionnel)</label>
        <input type="url" id="external_url" name="external_url">

        <button class="button button-primary" type="submit">Créer la leçon</button>
      </form>
    </section>
  </main>

  <script>
    async function createLesson(e) {
      e.preventDefault();
      const form = document.getElementById('lesson-form');
      const fileInput = document.getElementById('file');
      const message = document.getElementById('message');
      message.textContent = '';

      // If file provided, upload first
      let uploadedPath = '';
      if (fileInput.files.length > 0) {
        const fd = new FormData();
        fd.append('file', fileInput.files[0]);
        const upResp = await fetch('../api/uploads.php', {method: 'POST', body: fd});
        const upRes = await upResp.json();
        if (!upRes.success) { message.textContent = upRes.message; message.className='message error'; return; }
        uploadedPath = upRes.path;
      }

      // create lesson
      const data = new FormData(form);
      if (uploadedPath) data.append('file_path', uploadedPath);
      const resp = await fetch('../api/lessons.php?action=create', {method: 'POST', body: data});
      const res = await resp.json();
      if (!res.success) { message.textContent = res.message; message.className='message error'; return; }
      message.textContent = 'Leçon créée (ID: ' + res.lesson_id + ')'; message.className='message success';
    }
  </script>
</body>
</html>
