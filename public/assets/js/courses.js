async function loadCourses() {
  const container = document.getElementById('courses-list');
  if (!container) return;
  container.innerHTML = '<p>Chargement des cours...</p>';

  try {
    const response = await fetch('api/courses.php?action=list');
    const result = await response.json();
    if (!result.success) {
      container.innerHTML = `<p class="message error">${result.message}</p>`;
      return;
    }

    if (!result.courses || result.courses.length === 0) {
      container.innerHTML = '<p>Aucun cours disponible pour le moment.</p>';
      return;
    }

    container.innerHTML = result.courses.map(course => {
      return `
        <article class="feature-card">
          <h2>${escapeHTML(course.title)}</h2>
          <p>${escapeHTML(course.description || 'Description non disponible.')}</p>
          <p class="muted">Promoteur : ${escapeHTML(course.promoter_name)}</p>
          <a class="button button-secondary" href="course.php?id=${course.id}">Voir le cours</a>
        </article>
      `;
    }).join('');
  } catch (error) {
    container.innerHTML = '<p class="message error">Impossible de charger les cours.</p>';
  }
}

function escapeHTML(text) {
  const element = document.createElement('div');
  element.textContent = text;
  return element.innerHTML;
}

window.addEventListener('DOMContentLoaded', loadCourses);
