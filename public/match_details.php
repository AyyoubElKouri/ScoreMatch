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
<?php

    // Récupérer les joueurs de l'équipe 1
    try {
      // Récupérer les joueurs de l'équipe 1 du match
      $stmt_joueurs_equipe1 = $pdo->prepare("
          SELECT j.nom, j.prenom, j.age, j.position
          FROM joueurs j
          JOIN matches m ON j.equipe_id = m.equipe1_id
          WHERE m.id = ?
          ORDER BY j.position
      ");
      $stmt_joueurs_equipe1->execute([$match_id]);
      $joueurs_equipe1 = $stmt_joueurs_equipe1->fetchAll(PDO::FETCH_ASSOC);
  
      // Récupérer les joueurs de l'équipe 2 du match
      $stmt_joueurs_equipe2 = $pdo->prepare("
          SELECT j.nom, j.prenom, j.age, j.position
          FROM joueurs j
          JOIN matches m ON j.equipe_id = m.equipe2_id
          WHERE m.id = ?
          ORDER BY j.position
      ");
      $stmt_joueurs_equipe2->execute([$match_id]);
      $joueurs_equipe2 = $stmt_joueurs_equipe2->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      die("Erreur lors de la récupération des joueurs : " . $e->getMessage());
  }
  
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détails du Match</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">

    <style>
      /* 🌙 Mode Sombre */
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

.dark-mode .table {
    background-color: #1c1c1c;
    color: white;
    border: 1px solid #444;
}

.dark-mode .table thead {
    background-color: #333;
    color: white;
}

.dark-mode .btn-secondary {
    background-color: #444;
    border-color: #444;
}

.dark-mode .btn-primary {
    background-color: #ff5722;
    border-color: #ff5722;
}

.dark-mode .btn-primary:hover {
    background-color: #e64a19;
    border-color: #e64a19;
}

.dark-mode input {
    background-color: #1e1e1e;
    color: white;
    border: 1px solid #444;
}

.dark-mode input::placeholder {
    color: #bbb;
}

.dark-mode img {
    filter: brightness(0.8);
}

    </style>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : '' ?>">

  <!-- Inclure la barre de navigation -->
<?php include 'navbar.php'; ?>



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

<!-- Liste des joueurs de chaque équipe -->
<!-- Liste des joueurs de chaque équipe -->
<section class="py-5">
    <div class="container">
        <h3 class="text-center mb-4">👥 Joueurs des Équipes</h3>

        <div class="row">
            <!-- Joueurs de l'équipe 1 -->
            <div class="col-md-6">
                <h4 class="text-center"><?= htmlspecialchars($match['equipe1']) ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Âge</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($joueurs_equipe1)) : ?>
                                <?php foreach ($joueurs_equipe1 as $joueur) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($joueur["nom"]) ?></td>
                                        <td><?= htmlspecialchars($joueur["prenom"]) ?></td>
                                        <td><?= htmlspecialchars($joueur["age"]) ?></td>
                                        <td><?= htmlspecialchars($joueur["position"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="4" class="text-center">Aucun joueur trouvé.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Joueurs de l'équipe 2 -->
            <div class="col-md-6">
                <h4 class="text-center"><?= htmlspecialchars($match['equipe2']) ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Âge</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($joueurs_equipe2)) : ?>
                                <?php foreach ($joueurs_equipe2 as $joueur) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($joueur["nom"]) ?></td>
                                        <td><?= htmlspecialchars($joueur["prenom"]) ?></td>
                                        <td><?= htmlspecialchars($joueur["age"]) ?></td>
                                        <td><?= htmlspecialchars($joueur["position"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="4" class="text-center">Aucun joueur trouvé.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
