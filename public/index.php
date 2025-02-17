<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil - Gestion des Matchs</title>

    <!-- Lien Bootstrap CSS -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>

    <!-- Barre de navigation Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">⚽ Gestion des Matchs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="matches.php">Matchs</a></li>
                    <li class="nav-item"><a class="nav-link" href="teams.php">Équipes</a></li>
                    <li class="nav-item"><a class="nav-link" href="tournaments.php">Tournois</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Tableau de Bord</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-success text-white" href="register.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Section principale -->
    <header class="bg-light text-center py-5">
        <div class="container">
            <h1 class="display-4">Bienvenue sur l'application de gestion des matchs de football</h1>
            <p class="lead">Suivez vos équipes préférées et consultez les derniers matchs !</p>
            <?php if (!$isLoggedIn): ?>
                <a href="register.php" class="btn btn-lg btn-primary">Créer un compte</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Section Publications -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Publications sur botola inwi pro</h2>
        <div class="row">
            <?php
            // Tableau de publications statiques
            $publications = [
                [
                    "titre" => "Wydad Casablanca en tête du championnat",
                    "contenu" => "Le Wydad continue de dominer la Botola Pro Inwi avec une série impressionnante de victoires.",
                    "image" => "../public/assets/images/waydad.png", // Remplace avec une image valide
                    "lien" => "#"
                ],
                [
                    "titre" => "Raja Casablanca prêt pour le sacre ?",
                    "contenu" => "Le Raja Club Athletic montre une belle forme cette saison et se positionne comme un sérieux prétendant au titre.",
                    "image" => "../public/assets/images/raja.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "RS Berkane impressionne en championnat",
                    "contenu" => "La Renaissance Sportive de Berkane réalise une excellente saison et vise les premières places du classement.",
                    "image" => "../public/assets/images/barkan.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "FUS Rabat surprend ses adversaires",
                    "contenu" => "Le Fath Union Sport de Rabat s'impose comme un sérieux challenger cette saison.",
                    "image" => "../public/assets/images/fus.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "Moghreb Tétouan de retour en force",
                    "contenu" => "Le MAT affiche de belles performances et espère se maintenir dans le haut du tableau.",
                    "image" => "../public/assets/images/tetouan.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "HUSA Agadir vise le haut du classement",
                    "contenu" => "Hassania Agadir mise sur une dynamique positive pour s’imposer cette saison.",
                    "image" => "../public/assets/images/agadir.png",
                    "lien" => "#"
                ]
            ];
            

            // Affichage des publications
            foreach ($publications as $publication) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card">';
                echo '<img src="' . $publication['image'] . '" class="card-img-top" alt="Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($publication['titre']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($publication['contenu']) . '</p>';
                echo '<a href="' . $publication['lien'] . '" class="btn btn-primary">Lire la suite</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>


    <!-- Section des matchs récents -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">Derniers Matchs</h2>
            <div class="row">
                <?php
                require_once '../config/database.php';

                // Récupérer les derniers matchs
                $query = "SELECT * FROM matchs ORDER BY date_match DESC LIMIT 5"; 

                $result = $pdo->query($query);

                while ($match = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='col-md-4 mb-3'>";
                    echo "<div class='card'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>Match ID: " . $match['id'] . "</h5>";
                    echo "<p class='card-text'>Date: " . $match['date'] . "</p>";
                    echo "<a href='match_details.php?id=" . $match['id'] . "' class='btn btn-primary'>Voir Détails</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Pied de page -->
  <!-- Pied de page -->
<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- Section Tournois -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Tournois</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Premier League</a></li>
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


    <!-- Lien vers jQuery -->
    <script src="../bootstrap-5.3.3-dist/js/jquery-3.7.1.min.js"></script>
    <!-- Lien vers Popper.js -->
    <script src="../bootstrap-5.3.3-dist/js/popper.min.js"></script>
    <!-- Lien vers Bootstrap JS -->
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>
