<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matchs de la Premier League</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">⚽ Gestion des Matchs</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="matches.php">Matchs</a></li>
                <li class="nav-item"><a class="nav-link" href="teams.php">Équipes</a></li>
                <li class="nav-item"><a class="nav-link" href="tournaments.php">Tournois</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Section des matchs -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Matchs de la Premier League</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php
                // Liste des matchs statiques de la Premier League
                $matches = [
                    [
                        "date" => "17 Février 2025",
                        "heure" => "18:30",
                        "equipe1" => "Liverpool",
                        "logo1" => "../public/assets/images/Liverpool-FC-logo.png",
                        "score" => "VS",
                        "equipe2" => "Chelsea",
                        "logo2" => "../public/assets/images/logo_chelse.png",
                        "lien" => "#"
                    ],
                    [
                        "date" => "18 Février 2025",
                        "heure" => "20:00",
                        "equipe1" => "Manchester United",
                        "logo1" => "../public/assets/images/Manchester-United-FC-logo.png",
                        "score" => "VS",
                        "equipe2" => "Arsenal",
                        "logo2" => "../public/assets/images/Arsenal-FC-logo.png",
                        "lien" => "#"
                    ],
                    [
                        "date" => "19 Février 2025",
                        "heure" => "19:45",
                        "equipe1" => "Tottenham",
                        "logo1" => "../public/assets/images/Tottenham-Hotspur-logo.png",
                        "score" => "VS",
                        "equipe2" => "Manchester City",
                        "logo2" => "../public/assets/images/Manchester-City-FC-logo.png",
                        "lien" => "#"
                    ]
                ];

                // Affichage des matchs sous forme de cartes
                foreach ($matches as $match) {
                    echo '<div class="card mb-4 shadow">';
                    echo '<div class="card-body text-center">';
                    echo '<p class="text-muted mb-1">' . htmlspecialchars($match["date"]) . ' | ' . htmlspecialchars($match["heure"]) . '</p>';
                    echo '<div class="d-flex align-items-center justify-content-center">';
                    echo '<div class="me-3 text-center">';
                    echo '<img src="' . htmlspecialchars($match["logo1"]) . '" class="img-fluid" alt="' . htmlspecialchars($match["equipe1"]) . '" style="width: 60px;">';
                    echo '<p class="mt-2"><strong>' . htmlspecialchars($match["equipe1"]) . '</strong></p>';
                    echo '</div>';
                    echo '<h3 class="mx-3">' . htmlspecialchars($match["score"]) . '</h3>';
                    echo '<div class="ms-3 text-center">';
                    echo '<img src="' . htmlspecialchars($match["logo2"]) . '" class="img-fluid" alt="' . htmlspecialchars($match["equipe2"]) . '" style="width: 60px;">';
                    echo '<p class="mt-2"><strong>' . htmlspecialchars($match["equipe2"]) . '</strong></p>';
                    echo '</div>';
                    echo '</div>';
                    echo '<a href="' . htmlspecialchars($match["lien"]) . '" class="btn btn-primary mt-3">Voir Détails</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Pied de page -->
<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 Gestion des Matchs - Tous droits réservés.</p>
</footer>

<!-- Bootstrap JS -->
<script src="../bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>
