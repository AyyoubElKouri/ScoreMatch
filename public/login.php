<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe avec password_verify()
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        // Rediriger selon le rôle
        if ($user['role'] == 'admin_global') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] == 'admin_tournoi') {
            header("Location: tournoi_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
 <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
<!-- Custom CSS -->
 <style>
  /* =========================== */
/* ==== GLOBAL STYLES ==== */
/* =========================== */
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  transition: background 0.3s ease-in-out, color 0.3s ease-in-out;
}

/* =========================== */
/* ==== CONTAINER LOGIN ==== */
/* =========================== */
.login-container {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
  width: 350px;
  text-align: center;
  transition: all 0.3s ease-in-out;
}

/* Titre */
.login-container h2 {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
  color: #333;
}

/* Formulaire */
.login-container .form-label {
  font-weight: 500;
  color: #555;
}

.login-container .form-control {
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  transition: border 0.3s ease-in-out;
}

.login-container .form-control:focus {
  border-color: #FF5722;
  box-shadow: 0 0 5px rgba(255, 87, 34, 0.3);
}

/* Bouton de connexion (Bootstrap + Custom) */
.login-container .btn-primary {
  background-color: #FF5722;
  border: none;
  font-size: 16px;
  padding: 12px;
  border-radius: 8px;
  font-weight: bold;
  transition: background 0.3s ease-in-out, transform 0.2s ease-in-out;
}

.login-container .btn-primary:hover {
  background-color: #E64A19;
  transform: scale(1.05);
}

/* Message d'erreur Bootstrap */
.alert-danger {
  font-size: 14px;
  border-radius: 8px;
}

/* Lien d'inscription */
.login-container p {
  font-size: 14px;
  margin-top: 15px;
}

.login-container a {
  color: #FF5722;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease-in-out;
}

.login-container a:hover {
  color: #E64A19;
  text-decoration: underline;
}

/* =========================== */
/* ==== MODE SOMBRE (DARK MODE) ==== */
/* =========================== */
.dark-mode {
  background-color: #121212;
  color: white;
}

.dark-mode .login-container {
  background: #1e1e1e;
  box-shadow: 0 5px 15px rgba(255, 87, 34, 0.3);
}

.dark-mode .login-container h2 {
  color: white;
}

.dark-mode .login-container .form-label {
  color: #bbb;
}

.dark-mode .login-container .form-control {
  background: #2c2c2c;
  color: white;
  border: 1px solid #555;
}

.dark-mode .login-container .form-control:focus {
  border-color: #FF5722;
  box-shadow: 0 0 5px rgba(255, 87, 34, 0.5);
}

.dark-mode .login-container .btn-primary {
  background-color: #FF5722;
}

.dark-mode .login-container .btn-primary:hover {
  background-color: #E64A19;
}

.dark-mode .login-container a {
  color: #FF5722;
}

.dark-mode .login-container a:hover {
  color: #E64A19;
}

 </style>


</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

<div class="card shadow p-4" style="width: 350px;">
    <h2 class="text-center">Connexion</h2>
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>
    <p class="text-center mt-3">Pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
</div>

</body>
</html>
