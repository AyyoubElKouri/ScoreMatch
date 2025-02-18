<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'ID du match est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: matches.php");
    exit();
}

$match_id = $_GET['id'];

// Récupérer les détails du match
$query = "
    SELECT m.id, m.date_match, m.heure, 
           e1.nom AS equipe1, e1.logo AS logo1, 
           e2.nom AS equipe2, e2.logo AS logo2, 
           s.nom AS stade, s.ville, s.capacite, s.image AS stade_logo
    FROM matches m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    LEFT JOIN stades s ON m.stade_id = s.id
    WHERE m.id = ?
";

$stmt = $pdo->prepare($query);
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    echo "<p>Match introuvable.</p>";
    exit();
}

// Récupérer le nombre total de matchs joués dans le tournoi
$total_matchs = $pdo->query("SELECT COUNT(*) AS total FROM matches")->fetch(PDO::FETCH_ASSOC)['total'];

// Récupérer le nombre de matchs joués par chaque équipe
$query = "
    SELECT e.nom, COUNT(m.id) AS matchs_joues
    FROM matches m
    JOIN equipes e ON e.id = m.equipe1_id OR e.id = m.equipe2_id
    WHERE e.id IN (?, ?)
    GROUP BY e.nom
";
$stmt = $pdo->prepare($query);
$stmt->execute([$match['equipe1'], $match['equipe2']]);
$matchs_par_equipe = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détails du Match</title>
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

<!-- Détails du match -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Détails du Match</h2>

        <div class="card shadow p-4">
            <div class="text-center">
                <p class="text-muted"><strong>Date :</strong> <?= htmlspecialchars($match['date_match']) ?></p>
                <p class="text-muted"><strong>Heure :</strong> <?= htmlspecialchars($match['heure']) ?></p>
            </div>

            <div class="row text-center align-items-center">
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="<?= htmlspecialchars($match['equipe1']) ?>" width="100">
                    <h4><?= htmlspecialchars($match['equipe1']) ?></h4>
                </div>
                <div class="col-md-4">
                    <h3>VS</h3>
                </div>
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="<?= htmlspecialchars($match['equipe2']) ?>" width="100">
                    <h4><?= htmlspecialchars($match['equipe2']) ?></h4>
                </div>
            </div>
        <div class="text-center mt-4">

        <h5><strong>Stade :</strong> <?= htmlspecialchars($match['stade']) ?> (<?= htmlspecialchars($match['ville']) ?>)</h5>
        <p><strong>Capacité :</strong> <?= number_format($match['capacite']) ?> spectateurs</p>

       <?php if (!empty($match['stade_logo'])) : ?>
            <div class="mt-3">
               <img src="<?= htmlspecialchars($match['stade_logo']) ?>" alt="Logo du stade" width="150" class="img-fluid rounded">
            </div>
      <?php endif; ?>
  </div>
        </div>

        <!-- Statistiques -->
        <div class="card shadow p-4 mt-4">
            <h3 class="text-center mb-4">Statistiques du Match</h3>
            <ul>
                <li><strong>Nombre total de matchs dans le tournoi :</strong> <?= $total_matchs ?></li>
                <?php foreach ($matchs_par_equipe as $stat) : ?>
                    <li><strong><?= htmlspecialchars($stat['nom']) ?> :</strong> <?= $stat['matchs_joues'] ?> matchs joués</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- Pied de page -->
<footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2025 Gestion des Matchs - Tous droits réservés.</p>
</footer>

<!-- Bootstrap JS -->
<script src="../bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>
