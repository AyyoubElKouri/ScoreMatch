<?php
session_start();
require_once '../config/database.php'; // Connexion à la base de données

// Récupérer les équipes depuis la base de données
try {
    $stmt = $pdo->query("SELECT id, nom, logo FROM equipes ORDER BY nom");
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Équipes - Botola Pro Inwi</title>
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

<!-- Section des équipes -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Équipes de Botola Pro Inwi</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php
                if (!empty($teams)) {
                    foreach ($teams as $team) {
                        echo '<div class="card mb-3 shadow">';
                        echo '<div class="row g-0 align-items-center">';
                        echo '<div class="col-md-4 text-center">';
                        echo '<img src="' . htmlspecialchars($team["logo"]) . '" class="img-fluid rounded-start p-3" alt="' . htmlspecialchars($team["nom"]) . '" style="max-width: 100px;">';
                        echo '</div>';
                        echo '<div class="col-md-8">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($team["nom"]) . '</h5>';
                        echo '<a href="team_details.php?id=' . $team["id"] . '" class="btn btn-primary">Voir Détails</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="text-center">Aucune équipe trouvée.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
