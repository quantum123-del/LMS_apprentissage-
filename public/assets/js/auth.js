function showMessage(message, type = 'error') {
  const messageEl = document.getElementById('message');
  if (!messageEl) {
    return;
  }
  messageEl.textContent = message;
  messageEl.className = `message ${type}`;
}

async function login(event) {
  event.preventDefault();
  showMessage('', '');

  const form = document.getElementById('login-form');
  const data = new FormData(form);

  try {
    const response = await fetch('api/auth.php?action=login', {
      method: 'POST',
      body: data,
    });
    const result = await response.json();
    if (!result.success) {
      showMessage(result.message, 'error');
      return;
    }
    showMessage(result.message, 'success');
    window.location.href = 'dashboard.php';
  } catch (error) {
    showMessage('Impossible de contacter le serveur.', 'error');
  }
}

async function registerUser(event) {
  event.preventDefault();
  showMessage('', '');

  const form = document.getElementById('register-form');
  const data = new FormData(form);

  try {
    const response = await fetch('api/auth.php?action=register', {
      method: 'POST',
      body: data,
    });
    const result = await response.json();
    if (!result.success) {
      showMessage(result.message, 'error');
      return;
    }
    showMessage(result.message, 'success');
    setTimeout(() => {
      window.location.href = 'login.php';
    }, 1200);
  } catch (error) {
    showMessage('Impossible de contacter le serveur.', 'error');
  }
}
