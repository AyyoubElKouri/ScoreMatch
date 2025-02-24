<?php
session_start();
require_once '../config/database.php';

// Récupérer les matchs ayant un score enregistré
$query = "SELECT m.*, 
                 e1.nom AS equipe1, e2.nom AS equipe2, 
                 e1.logo AS logo1, e2.logo AS logo2,
                 m.score_equipe1, m.score_equipe2
          FROM matches m
          JOIN equipes e1 ON m.equipe1_id = e1.id
          JOIN equipes e2 ON m.equipe2_id = e2.id
          WHERE m.score_equipe1 IS NOT NULL AND m.score_equipe2 IS NOT NULL
          ORDER BY m.date_match DESC";

$matchs_resultats = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats des Matchs</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../public/assets/css/resultats.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Résultats des Matchs</h2>
    <div class="match-list">
        <?php foreach ($matchs_resultats as $match): ?>
            <div class="match-item" onclick="goToDetails(<?= $match['id'] ?>)">
                <div class="match-info">
                    <span class="match-date"><?= date('d.m H:i', strtotime($match['date_match'])) ?></span>
                </div>
                <div class="match-content">
                    <div class="team">
                        <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="Équipe 1">
                    </div>
                    <div class="match-score">
                        <?= htmlspecialchars($match['score_equipe1']) ?> - <?= htmlspecialchars($match['score_equipe2']) ?>
                    </div>
                    <div class="team">
                        <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="Équipe 2">
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function goToDetails(matchId) {
    window.location.href = "detail_resultat.php?match_id=" + matchId;
}
</script>

</body>
</html>
