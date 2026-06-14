async function loadQuizForLesson(lessonId) {
  const area = document.getElementById('quiz-area');
  area.innerHTML = '<p>Chargement de l\'évaluation...</p>';
  try {
    const resp = await fetch(`api/quizzes.php?action=get&lesson_id=${lessonId}`);
    const res = await resp.json();
    if (!res.success) { area.innerHTML = `<p>${res.message}</p>`; return; }
    const quiz = res.quiz;
    const questions = res.questions;
    area.innerHTML = `<h2>${escapeHTML(quiz.title)}</h2><form id="quiz-form"></form>`;
    const form = document.getElementById('quiz-form');
    questions.forEach(q => {
      const qDiv = document.createElement('div');
      qDiv.className = 'feature-card';
      qDiv.innerHTML = `<h3>${escapeHTML(q.text)}</h3>`;
      if (q.type === 'mcq') {
        q.choices.forEach(choice => {
          const id = 'choice_' + choice.id;
          const input = `<label><input type="radio" name="q_${q.id}" value="${choice.id}"> ${escapeHTML(choice.text)}</label>`;
          qDiv.innerHTML += input;
        });
      } else {
        qDiv.innerHTML += `<textarea name="q_${q.id}" rows="3" placeholder="Réponse"></textarea>`;
      }
      form.appendChild(qDiv);
    });
    form.innerHTML += '<button class="button button-primary" type="button" id="submit-quiz">Soumettre</button>';
    document.getElementById('submit-quiz').addEventListener('click', () => submitQuiz(quiz.id));
  } catch (e) {
    area.innerHTML = '<p>Erreur lors du chargement du quiz.</p>';
  }
}

function escapeHTML(text) { const el = document.createElement('div'); el.textContent = text; return el.innerHTML; }

async function submitQuiz(quizId) {
  const form = document.getElementById('quiz-form');
  const inputs = form.elements;
  const answers = [];
  for (let el of inputs) {
    if (!el.name) continue;
    if (el.name.startsWith('q_')) {
      const qId = parseInt(el.name.replace('q_', ''), 10);
      if (el.type === 'radio') {
        if (el.checked) answers.push({question_id: qId, choice_id: parseInt(el.value, 10)});
      } else if (el.tagName.toLowerCase() === 'textarea') {
        answers.push({question_id: qId, text: el.value});
      }
    }
  }

  try {
    const resp = await fetch('api/attempts.php', {method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({quiz_id: quizId, answers})});
    const res = await resp.json();
    const area = document.getElementById('quiz-area');
    if (!res.success) { area.innerHTML = `<p class="message error">${res.message}</p>`; return; }
    area.innerHTML = `<p class="message success">Résultat: ${res.score}/${res.max_score} — Progression module: ${Math.round(res.progress)}% — ${res.passed ? 'Validé' : 'Non validé'}</p>`;
  } catch (e) {
    const area = document.getElementById('quiz-area');
    area.innerHTML = '<p class="message error">Impossible de soumettre l\'évaluation.</p>';
  }
}
