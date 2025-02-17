<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_global') {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

// Récupérer quelques statistiques pour l'Admin Global
$nb_equipes = $pdo->query("SELECT COUNT(*) FROM equipes")->fetchColumn();
$nb_matchs = $pdo->query("SELECT COUNT(*) FROM matchs")->fetchColumn();
$nb_staff = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de Bord Admin Global</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
    <script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script> <!-- FontAwesome pour icônes -->
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand text-white" href="#">⚽ Admin Global - Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Se Déconnecter</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Bienvenue, Admin Global</h2>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Équipes</h5>
                    <p class="card-text fs-3"><?= $nb_equipes; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Matchs</h5>
                    <p class="card-text fs-3"><?= $nb_matchs; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Staff</h5>
                    <p class="card-text fs-3"><?= $nb_staff; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu de gestion -->
    <div class="list-group mt-4">
        <a href="admin_equipes.php" class="list-group-item list-group-item-action">
            <i class="fas fa-users"></i> Gérer les Équipes
        </a>
        <a href="admin_matchs.php" class="list-group-item list-group-item-action">
            <i class="fas fa-futbol"></i> Gérer les Matchs
        </a>
        <a href="admin_staff.php" class="list-group-item list-group-item-action">
            <i class="fas fa-user-tie"></i> Gérer le Staff des Équipes
        </a>
        <a href="logout.php" class="list-group-item list-group-item-action text-danger">
            <i class="fas fa-sign-out-alt"></i> Se Déconnecter
        </a>
    </div>
</div>

</body>
</html>
