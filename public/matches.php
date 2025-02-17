<?php
session_start();
require_once '../config/database.php';

// Récupérer tous les matchs depuis la base de données
$matchs = $pdo->query("
    SELECT m.id, e1.nom AS equipe1, e2.nom AS equipe2, e1.logo AS logo1, e2.logo AS logo2, m.date_match, m.heure 
    FROM matches m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    ORDER BY m.date_match DESC
")->fetchAll(PDO::FETCH_ASSOC);

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
            <?php foreach ($matchs as $match) : ?>
    <div class="card mb-4 shadow">
        <div class="card-body text-center">
            <p class="text-muted mb-1"><?= htmlspecialchars($match['date_match']) . " | " . htmlspecialchars($match['heure']) ?></p>
            <div class="d-flex align-items-center justify-content-center">
                <div class="me-3 text-center">
                    <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="Logo <?= htmlspecialchars($match['equipe1']) ?>" class="img-fluid" style="width: 60px;">
                    <p class="mt-2"><strong><?= htmlspecialchars($match['equipe1']) ?></strong></p>
                </div>
                <h3 class="mx-3">VS</h3>
                <div class="ms-3 text-center">
                    <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="Logo <?= htmlspecialchars($match['equipe2']) ?>" class="img-fluid" style="width: 60px;">
                    <p class="mt-2"><strong><?= htmlspecialchars($match['equipe2']) ?></strong></p>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


            </div>
        </div>
    </div>
</section>

<!-- Pied de page -->
<footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">&copy; 2025 Gestion des Matchs - Tous droits réservés.</p>
</footer>


<!-- Bootstrap JS -->
<script src="../bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>
