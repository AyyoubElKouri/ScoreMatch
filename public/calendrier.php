<?php
session_start();
require_once '../config/database.php';

// Récupérer tous les matchs depuis la base de données
$matchs = $pdo->query("
    SELECT m.id, e1.nom AS equipe1, e2.nom AS equipe2, e1.logo AS logo1, e2.logo AS logo2, 
           m.date_match AS match_date, 
           m.heure AS match_time
    FROM matches m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    ORDER BY m.date_match ASC
")->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matchs de Botola Pro Inwi</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../public/assets/css/calendrier.css">

  
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : '' ?>">


<!-- Inclure la barre de navigation -->
<?php include 'navbar.php'; ?>


<!-- Section des matchs -->
<!-- Section des matchs -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Matchs de Botola Pro</h2>
        <div class="match-list">
            <?php foreach ($matchs as $match) : ?>
                <div class="match-card">
                    <!-- Date et Heure -->
                    <div class="match-info">
                    <?php
                        if (isset($match['match_date'])) {
                        $formatted_date = date('d/m/Y', strtotime($match['match_date']));
                  } else {
                        $formatted_date = "Date inconnue";
                }

                    if (!empty($match['match_time']) && $match['match_time'] !== "00:00:00") {
                    $formatted_time = date('H:i', strtotime($match['match_time']));
                } else {
                   $formatted_time = ''; // On ne l'affiche pas si c'est 00:00:00
                }

               // Affichage final
               $display_date = $formatted_date . (!empty($formatted_time) ? " " . $formatted_time : '');
          ?>
              <span><?= $display_date ?></span>

                    </div>
                    <!-- Logos des équipes -->
                    <div class="match-content">
                        <div class="team">
                            <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="Équipe 1">
                            <p class="team-name"><?= htmlspecialchars($match['equipe1']) ?></p>
                        </div>
                        <div class="match-vs">VS</div>
                        <div class="team">
                            <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="Équipe 2">
                            <p class="team-name"><?= htmlspecialchars($match['equipe2']) ?></p>
                        </div>
                    </div>
                    <!-- Bouton Détails -->
                    <a href="match_details.php?id=<?= $match['id'] ?>" class="match-details-btn">Détails</a>
                </div>
            <?php endforeach; ?>
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
