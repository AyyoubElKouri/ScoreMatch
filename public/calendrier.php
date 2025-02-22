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
    <title>Matchs de Botola Pro Inwi</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">

    <style>
        /* Mode sombre pour la page calendrier */
.dark-mode {
    background-color: #121212;
    color: white;
}

.dark-mode .card {
    background-color: #1e1e1e;
    color: white;
    border: 1px solid #444;
}

.dark-mode .text-muted {
    color: #bbb !important;
}

.dark-mode .card-body {
    background-color: #1c1c1c;
    border-radius: 10px;
}

    </style>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : '' ?>">


<!-- Inclure la barre de navigation -->
<?php include 'navbar.php'; ?>

<!-- Section des matchs -->
<!-- Section des matchs -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Matchs de Botola Pro </h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php foreach ($matchs as $match) : ?>
                    <div class="card mb-4 shadow match-card">
                        <div class="card-body text-center">
                            <p class="text-muted mb-1"><?= htmlspecialchars($match['date_match']) . " | " . htmlspecialchars($match['heure']) ?></p>
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="me-3 text-center">
                                    <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="Logo <?= htmlspecialchars($match['equipe1']) ?>" class="img-fluid" style="width: 50px;">
                                    <p class="mt-2"><strong><?= htmlspecialchars($match['equipe1']) ?></strong></p>
                                </div>
                                <h3 class="mx-3">VS</h3>
                                <div class="ms-3 text-center">
                                    <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="Logo <?= htmlspecialchars($match['equipe2']) ?>" class="img-fluid" style="width: 50px;">
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
