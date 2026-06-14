async function loadLesson(lessonId) {
  const titleEl = document.getElementById('lesson-title');
  const contentEl = document.getElementById('lesson-content');
  titleEl.textContent = 'Chargement...';
  contentEl.innerHTML = '';

  try {
    const resp = await fetch(`api/lessons.php?action=detail&id=${lessonId}`);
    const res = await resp.json();
    if (!res.success) { titleEl.textContent = 'Erreur'; contentEl.innerHTML = `<p>${res.message}</p>`; return; }
    const lesson = res.lesson;
    titleEl.textContent = lesson.title;
    if (lesson.type === 'pdf' && lesson.file_path) {
      contentEl.innerHTML = `<embed src="${escapeHTML(lesson.file_path)}" type="application/pdf" width="100%" height="600px"/>`;
    } else if (lesson.type === 'video' && lesson.file_path) {
      contentEl.innerHTML = `<video controls width="100%"><source src="${escapeHTML(lesson.file_path)}" type="video/mp4">Votre navigateur ne supporte pas la vidéo.</video>`;
    } else if (lesson.external_url) {
      contentEl.innerHTML = `<a href="${escapeHTML(lesson.external_url)}" target="_blank">Ouvrir le contenu externe</a>`;
    } else {
      contentEl.innerHTML = '<p>Contenu non disponible.</p>';
    }

    // load quiz
    loadQuizForLesson(lessonId);
  } catch (e) {
    titleEl.textContent = 'Erreur chargement';
  }
}

function escapeHTML(text) {
  const el = document.createElement('div'); el.textContent = text; return el.innerHTML;
}

window.addEventListener('DOMContentLoaded', () => {
  if (typeof LESSON_ID === 'number' && LESSON_ID > 0) loadLesson(LESSON_ID);
});
