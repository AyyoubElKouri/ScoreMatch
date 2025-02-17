
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Équipes - Premier League</title>
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
                <li class="nav-item"><a class="nav-link" href="matches.php">Matchs</a></li>
                <li class="nav-item"><a class="nav-link active" href="teams.php">Équipes</a></li>
                <li class="nav-item"><a class="nav-link" href="tournaments.php">Tournois</a></li>
            </ul>
        </div>
    </div>
</nav>




<!-- Section des équipes en format vertical -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Équipes de la Premier League</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php
                // Liste statique des équipes de Premier League
                $teams = [
                    ["nom" => "Liverpool", "logo" => "../public/assets/images/Liverpool-FC-logo.png", "lien" => "team.php?team=liverpool"],
                    ["nom" => "Arsenal", "logo" => "../public/assets/images/logo_ars.png", "lien" => "team.php?team=arsenal"],
                    ["nom" => "Manchester City", "logo" => "../public/assets/images/Manchester-City-FC-logo.png", "lien" => "team.php?team=manchestercity"],
                    ["nom" => "Manchester United", "logo" => "../public/assets/images//Manchester-United-FC-logo.png", "lien" => "team.php?team=manunited"],
                    ["nom" => "Tottenham", "logo" => "../public/assets/images/logo_totnham.png", "lien" => "team.php?team=tottenham"],
                    ["nom" => "Chelsea", "logo" => "../public/assets/images/logo_chelse.png", "lien" => "team.php?team=chelsea"]
                ];

                // Affichage vertical des équipes
                foreach ($teams as $team) {
                    echo '<div class="card mb-3 shadow">';
                    echo '<div class="row g-0 align-items-center">';
                    echo '<div class="col-md-4 text-center">';
                    echo '<img src="' . htmlspecialchars($team["logo"]) . '" class="img-fluid rounded-start p-3" alt="' . htmlspecialchars($team["nom"]) . '" style="max-width: 100px;">';
                    echo '</div>';
                    echo '<div class="col-md-8">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($team["nom"]) . '</h5>';
                    echo '<a href="' . htmlspecialchars($team["lien"]) . '" class="btn btn-primary">Voir l’équipe</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>
