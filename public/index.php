<?php
session_start();

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; // 'guest' par défaut

require_once '../config/database.php';

// Récupérer les matchs du jour
$today = date('Y-m-d');
$query = "SELECT m.*, 
                 e1.nom AS equipe1, e2.nom AS equipe2, 
                 e1.logo AS logo1, e2.logo AS logo2 
          FROM matches m
          JOIN equipes e1 ON m.equipe1_id = e1.id
          JOIN equipes e2 ON m.equipe2_id = e2.id
          WHERE DATE(m.date_match) = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$today]);
$matchs_du_jour = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scores Matches</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
    
    <!-- Custom Styles -->
  
   <style>
    /* ----- Global Styles ----- */
body {
    transition: background 0.3s, color 0.3s;
}

/* Mode Sombre */
.dark-mode {
    background-color: #121212;
    color: white;
}

.dark-mode .navbar, .dark-mode .match-card, .dark-mode .publication-card {
    background-color: #1c1c1c;
    border-color: #333;
}

.dark-mode .publication-card:hover {
    background-color: #2a2a2a;
}

.dark-mode .text-muted, .dark-mode .publication-meta {
    color: #bbb !important;
}

.dark-mode .card {
    background-color: #1e1e1e;
    color: white;
}

/* ----- Navbar ----- */
.navbar {
    background-color: #0D1B2A;
    padding: 12px 20px;
    border-bottom: 2px solid #FF5722;
}

.navbar-brand {
    font-size: 20px;
    font-weight: bold;
    color: white;
    display: flex;
    align-items: center;
}

.navbar-nav .nav-link {
    color: white;
    font-weight: bold;
    padding: 8px 12px;
    transition: 0.3s;
}

.navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
    background: #FF5722;
}

/* ----- Match Cards ----- */
.match-card {
    background-color: white;
    border-radius: 10px;
    padding: 15px;
    transition: 0.3s;
    border: 2px solid #ddd;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.match-card img {
    width: 45px;
    height: auto;
}

.team-name {
    font-size: 13px;
    font-weight: bold;
}

.match-card .btn {
    font-size: 12px;
    padding: 5px 12px;
    background-color: #FF5722;
    border: none;
}

/* ----- Publications ----- */
.publication-card {
    background-color: #0D1B2A;
    border-radius: 10px;
    padding: 15px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    color: white;
}

.publication-card:hover {
    background-color: #1C2A3A;
}

.publication-img {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 15px;
}

.publication-content {
    flex-grow: 1;
}

.publication-title {
    font-size: 18px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

.publication-title:hover {
    text-decoration: underline;
}

.publication-meta {
    font-size: 14px;
    color: #b0b3b8;
    margin-top: 5px;
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
                <li class="nav-item"><a class="nav-link " href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="calendrier.php">Calendrier</a></li>
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



    <!-- Section des matchs du jour -->
<section class="container mt-4">
    <h2 class="text-center">Matchs du jour</h2>
    <div class="row justify-content-center">
        <?php foreach ($matchs_du_jour as $match): ?>
            <div class="col-md-3"> <!-- Réduction de la largeur -->
                <div class="card match-card shadow">
                    <div class="card-body text-center">
                        <p class="text-muted"><strong>Heure:</strong> <?= date('H:i', strtotime($match['heure'])) ?></p> 
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="text-center me-2">
                                <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="<?= htmlspecialchars($match['equipe1']) ?>">
                                <p class="mt-2 team-name"><?= htmlspecialchars($match['equipe1']) ?></p>
                            </div>
                            <h4 class="mx-2">VS</h4>
                            <div class="text-center ms-2">
                                <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="<?= htmlspecialchars($match['equipe2']) ?>">
                                <p class="mt-2 team-name"><?= htmlspecialchars($match['equipe2']) ?></p>
                            </div>
                        </div>
                        <a href="match_details.php?id=<?= $match['id'] ?>" class="btn btn-primary btn-sm mt-2">Voir Détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

 <!-- Section Publications -->
 <?php
// Récupérer les publications depuis la base de données
$query = "SELECT * FROM publications ORDER BY date_publication DESC LIMIT 6"; // Limite à 6 publications
$publications = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Section Publications -->
<!-- Section Publications -->
<section class="py-5 publications">
    <div class="container">
        <h2 class="mb-4 text-center">Publications sur Botola Pro</h2>
        <div class="row">
            <?php foreach ($publications as $publication) : ?>
                <div class="col-md-12 mb-3">
                    <div class="publication-card d-flex align-items-center p-3 shadow">
                        <img src="<?= !empty($publication['image']) ? '../public/assets/images/' . htmlspecialchars($publication['image']) : '../public/assets/images/default.png'; ?>" class="publication-img" alt="Image">
                        <div class="publication-content">
                            <a href="#" class="publication-title"><?= htmlspecialchars($publication['titre']) ?></a>
                            <p class="publication-meta"><i class="fas fa-newspaper"></i> Publié le <?= date('d.m.Y H:i', strtotime($publication['date_publication'])) ?></p>
                            <p class="text-muted"><?= htmlspecialchars(substr($publication['contenu'], 0, 100)) ?>...</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>




    <!-- Section des matchs récents -->
    
    <?php
        require_once '../config/database.php';

        // Récupérer les derniers matchs
        $query = "SELECT m.*, 
            e1.nom AS equipe1, e2.nom AS equipe2, 
            e1.logo AS logo1, e2.logo AS logo2 
            FROM matches m
            JOIN equipes e1 ON m.equipe1_id = e1.id
            JOIN equipes e2 ON m.equipe2_id = e2.id
            ORDER BY m.date_match DESC 
            LIMIT 5";

      $result = $pdo->query($query);
      $dernier_matchs = $result->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Derniers Matchs</h2>
        <div class="row justify-content-center">
            <?php foreach ($dernier_matchs as $match): ?>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card match-card shadow text-center p-3">
                        <p class="text-muted">
                            <?= date('d.m H:i', strtotime($match['date_match'])) ?>
                        </p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="me-2 text-center">
                                <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="<?= htmlspecialchars($match['equipe1']) ?>" class="team-logo">
                                <p class="team-name"><?= htmlspecialchars($match['equipe1']) ?></p>
                            </div>
                            <h5 class="mx-2">VS</h5>
                            <div class="ms-2 text-center">
                                <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="<?= htmlspecialchars($match['equipe2']) ?>" class="team-logo">
                                <p class="team-name"><?= htmlspecialchars($match['equipe2']) ?></p>
                            </div>
                        </div>
                        <a href="match_details.php?id=<?= $match['id'] ?>" class="btn btn-sm btn-primary mt-2">Voir Détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

      

  <!-- Pied de page -->
<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- Section Tournois -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Tournois</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">botola inwi pro</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Classement</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Résultats</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Statistiques</a></li>
                </ul>
            </div>

            <!-- Section Matchs -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Matchs</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Matchs en direct</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Matchs d'aujourd'hui</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Matchs à venir</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Matchs d'hier</a></li>
                </ul>
            </div>

            <!-- Section Équipes -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Équipes</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Manchester City</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Arsenal</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Liverpool</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Manchester United</a></li>
                </ul>
            </div>

            <!-- Section Contact & Réseaux sociaux -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Contact</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">À propos</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Politique de confidentialité</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Conditions d'utilisation</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Contactez-nous</a></li>
                </ul>
                <div class="mt-3">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="text-center mt-4">
            <p class="mb-0">&copy; 2025 Gestion des Matchs - Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Font Awesome pour les icônes -->
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>


<!-- JavaScript pour le Mode Sombre -->
<script>
    function toggleTheme() {
        document.body.classList.toggle("dark-mode");
        let isDarkMode = document.body.classList.contains("dark-mode");
        localStorage.setItem("theme", isDarkMode ? "dark" : "light");
    }

    // Appliquer le thème sauvegardé
    document.addEventListener("DOMContentLoaded", function () {
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
    });
</script>


<!-- Bootstrap JS -->
<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
