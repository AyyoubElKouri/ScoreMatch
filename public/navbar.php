<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
<style>
/* ----- NAVIGATION PRINCIPALE ----- */
.navbar {
    background-color: #0D1B2A;
    padding: 12px 20px;
    border-bottom: 2px solid #FF5722;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-brand {
    font-size: 20px;
    font-weight: bold;
    color: white;
    display: flex;
    align-items: center;
}

.navbar-brand img {
    width: 40px;
    height: auto;
    margin-right: 10px;
}

.navbar-nav .nav-item {
    margin-right: 10px;
}

.navbar-nav .nav-link {
    color: white;
    font-size: 14px;
    font-weight: bold;
    padding: 8px 12px;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
    background: #FF5722;
    color: white;
}

/* ----- BOUTONS NAVIGATION ----- */
.nav-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 18px;
    margin-left: 8px;
}

.nav-btn:hover {
    color: #FF5722;
}

/* ----- MODE SOMBRE ----- */
body.dark-mode {
    background-color: #121212;
    color: white;
}

.dark-mode .navbar {
    background-color: #1c1c1c;
    border-bottom: 2px solid #FF5722;
}

.dark-mode .navbar-nav .nav-link {
    color: #ccc;
}

.dark-mode .navbar-nav .nav-link:hover,
.dark-mode .navbar-nav .nav-link.active {
    background: #FF5722;
    color: white;
}

.theme-switch {
    cursor: pointer;
    font-size: 20px;
    margin-left: 10px;
    color: white;
}
</style>

</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../public/assets/logo.png" alt="Logo"> <strong>Scores Matches</strong>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="calendrier.php">calendrier</a></li>
                <li class="nav-item"><a class="nav-link" href="teams.php">Équipes</a></li>
                <li class="nav-item"><a class="nav-link" href="tournaments.php">Tournois</a></li>
                <li class="nav-item"><a class="nav-link" href="classment.php">Classement</a></li>

                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'user'): ?>
                        <li class="nav-item"><a class="nav-link" href="vote_match.php">Voter un match</a></li>
                        <li class="nav-item"><a class="nav-link" href="discussion.php">Discuter un match</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Mon Profil</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="login.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-success text-white" href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>

            <!-- Bouton Mode Sombre -->
            <span class="theme-switch" onclick="toggleTheme()">🌙</span>
        </div>
    </div>
</nav>

  
</body>
</html>


<script>
    function toggleTheme() {
        document.body.classList.toggle("dark-mode");
        let isDarkMode = document.body.classList.contains("dark-mode");
        localStorage.setItem("theme", isDarkMode ? "dark" : "light");
    }

    document.addEventListener("DOMContentLoaded", function () {
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
    });
</script>
