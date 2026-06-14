<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription | LMS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="container auth-page">
    <section class="auth-card">
      <h1>Inscription</h1>
      <p>Créez un compte étudiant pour commencer à suivre les cours.</p>
      <div id="message" class="message"></div>
      <form id="register-form" onsubmit="registerUser(event)">
        <label for="name">Nom complet</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" minlength="6" required>

        <button class="button button-primary" type="submit">S'inscrire</button>
      </form>
      <p class="auth-footer">Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
    </section>
  </main>
  <script src="assets/js/auth.js"></script>
</body>
</html>
