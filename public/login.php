<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion | LMS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="container auth-page">
    <section class="auth-card">
      <h1>Connexion</h1>
      <p>Accédez à votre espace enseignant, étudiant ou promoteur.</p>
      <div id="message" class="message"></div>
      <form id="login-form" onsubmit="login(event)">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button class="button button-primary" type="submit">Se connecter</button>
      </form>
      <p class="auth-footer">Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
    </section>
  </main>
  <script src="assets/js/auth.js"></script>
</body>
</html>
