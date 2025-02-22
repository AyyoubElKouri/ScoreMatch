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
<?php
try {
    // Récupérer les 4 membres du staff de l'équipe affichée
    $stmt_staff = $pdo->prepare("
        SELECT nom, prenom, role 
        FROM staff 
        WHERE equipe_id = ? 
        ORDER BY role 
        
    ");
    $stmt_staff->execute([$id]); // $id correspond à l'ID de l'équipe affichée
    $staffs = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération du staff : " . $e->getMessage());
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($team["nom"]) ?> - Détails</title>
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

    </style>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : '' ?>">

  <!-- Inclure la barre de navigation -->
<?php include 'navbar.php'; ?>



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

<!-- Liste des membres du staff de l'équipe -->

<?php if (!empty($staffs)) : ?>
    <section class="py-5">
        <div class="container">
            <h3 class="text-center mb-4">👔 Staff de l'équipe</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffs as $staff) : ?>
                            <tr>
                                <td><?= htmlspecialchars($staff["nom"]) ?></td>
                                <td><?= htmlspecialchars($staff["prenom"]) ?></td>
                                <td><?= htmlspecialchars($staff["role"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
<?php else : ?>
    <div class="text-center py-3">
        <p>Aucun staff trouvé pour cette équipe.</p>
    </div>
<?php endif; ?>





<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
