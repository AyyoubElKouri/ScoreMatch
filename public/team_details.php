<?php
session_start();
require_once '../config/database.php'; // Connexion à la base de données
// Vérifier si l'ID de l'équipe est bien présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("Aucune équipe spécifiée.");
}

$id = intval($_GET['id']); // Sécuriser l'ID

try {
  // Requête avec jointure pour récupérer les infos de l'équipe ET du stade
  $stmt = $pdo->prepare("
      SELECT equipes.*, stades.nom AS stade_nom, stades.ville AS stade_ville, 
             stades.capacite AS stade_capacite, stades.image AS stade_image
      FROM equipes
      LEFT JOIN stades ON equipes.stade_id = stades.id
      WHERE equipes.id = ?
  ");
  $stmt->execute([$id]);
  $team = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$team) {
      die("Équipe introuvable.");
  }
} catch (PDOException $e) {
  die("Erreur : " . $e->getMessage());
}

?>
<?php
try {
    // Requête pour récupérer les joueurs de l'équipe
    $stmt_joueurs = $pdo->prepare("
        SELECT nom, prenom, age, position 
        FROM joueurs 
        WHERE equipe_id = ?
        ORDER BY position
    ");
    //exécution de la requête
    $stmt_joueurs->execute([$id]);
    //récupération des résultats
    $joueurs = $stmt_joueurs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des joueurs : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($team["nom"]) ?> - Détails</title>
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
                <li class="nav-item"><a class="nav-link" href="teams.php">Équipes</a></li>
                <li class="nav-item"><a class="nav-link" href="tournaments.php">Tournois</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Détails de l'équipe -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center"><?= htmlspecialchars($team["nom"]) ?></h2>
        <div class="card shadow p-4">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="<?= htmlspecialchars($team["logo"]) ?>" class="img-fluid" alt="<?= htmlspecialchars($team["nom"]) ?>" style="max-width: 150px;">
                </div>
                <div class="col-md-8">
                    <p><strong>Entraîneur :</strong> <?= htmlspecialchars($team["entraineur"]) ?></p>
                    <p><strong>Description :</strong> <?= htmlspecialchars($team["description"]) ?></p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="teams.php" class="btn btn-secondary">Retour aux équipes</a>
        </div>
    </div>
</section>
<!-- Affichage des informations du stade -->
<?php if (!empty($team["stade_nom"])) : ?>
    <div class="card shadow p-4 mt-4">
        <h3 class="text-center">🏟️ Stade</h3>
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <img src="<?= htmlspecialchars($team["stade_image"]) ?>" class="img-fluid" 
                     alt="<?= htmlspecialchars($team["stade_nom"]) ?>" style="max-width: 250px;">
            </div>
            <div class="col-md-8">
                <p><strong>Nom :</strong> <?= htmlspecialchars($team["stade_nom"]) ?></p>
                <p><strong>Ville :</strong> <?= htmlspecialchars($team["stade_ville"]) ?></p>
                <p><strong>Capacité :</strong> <?= number_format($team["stade_capacite"]) ?> places</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Liste des joueurs de l'équipe -->
<?php if (!empty($joueurs)) : ?>
    <section class="py-5">
        <div class="container">
            <h3 class="text-center mb-4">👕 Joueurs de l'équipe</h3>
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
                        <?php foreach ($joueurs as $joueur) : ?>
                            <tr>
                                <td><?= htmlspecialchars($joueur["nom"]) ?></td>
                                <td><?= htmlspecialchars($joueur["prenom"]) ?></td>
                                <td><?= htmlspecialchars($joueur["age"]) ?></td>
                                <td><?= htmlspecialchars($joueur["position"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
<?php else : ?>
    <div class="text-center py-3">
        <p>Aucun joueur trouvé pour cette équipe.</p>
    </div>
<?php endif; ?>



<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
