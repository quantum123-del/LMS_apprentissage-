async function loadCourseDetails(courseId) {
  const titleEl = document.getElementById('course-title');
  const descriptionEl = document.getElementById('course-description');
  const moduleList = document.getElementById('module-list');

  titleEl.textContent = 'Chargement du cours...';
  descriptionEl.textContent = '';
  moduleList.innerHTML = '<p>Chargement des modules...</p>';

  try {
    const response = await fetch(`api/courses.php?action=detail&id=${courseId}`);
    const result = await response.json();

    if (!result.success) {
      titleEl.textContent = 'Erreur';
      moduleList.innerHTML = `<p class="message error">${result.message}</p>`;
      return;
    }

    titleEl.textContent = result.course.title;
    descriptionEl.textContent = result.course.description || 'Description non disponible.';

    if (!result.modules || result.modules.length === 0) {
      moduleList.innerHTML = '<p>Aucun module disponible pour ce cours.</p>';
      return;
    }

    moduleList.innerHTML = result.modules.map(module => `
      <article class="feature-card">
        <h2>${escapeHTML(module.title)}</h2>
        <p>${escapeHTML(module.description || 'Description du module non disponible.')}</p>
        <a class="button button-secondary" href="#">Voir les leçons</a>
      </article>
    `).join('');
  } catch (error) {
    titleEl.textContent = 'Erreur de chargement';
    moduleList.innerHTML = '<p class="message error">Impossible de charger les détails du cours.</p>';
  }
}

function escapeHTML(text) {
  const element = document.createElement('div');
  element.textContent = text;
  return element.innerHTML;
}

window.addEventListener('DOMContentLoaded', () => {
  if (typeof COURSE_ID === 'number' && COURSE_ID > 0) {
    loadCourseDetails(COURSE_ID);
  }
});
