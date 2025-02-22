<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est un admin_tournoi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_tournoi') {
    header("Location: index.php");
    exit();
}

// Récupérer les statistiques liées au tournoi
$nb_publications = $pdo->query("SELECT COUNT(*) FROM publications")->fetchColumn();
$nb_matchs = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();
$nb_equipes = $pdo->query("SELECT COUNT(*) FROM equipes")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Tournoi - Dashboard</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
    <script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script> <!-- FontAwesome -->

    <style>
        /* Styles pour la sidebar */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #212529;
            padding-top: 20px;
            overflow-y: auto; /* Scroll */
        }
        .sidebar a {
            padding: 12px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.2s;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 20px;
        }
        .sidebar .sidebar-header {
            text-align: center;
            font-size: 20px;
            color: white;
            font-weight: bold;
            padding-bottom: 10px;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }

        /* Styles des cartes de statistiques */
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            color: white;
            text-align: center;
            transition: 0.3s;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }
        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.3);
        }
        .stat-icon {
            font-size: 40px;
            opacity: 0.8;
        }
        
        /* Couleurs */
        .bg-blue { background-color: #007bff; }
        .bg-green { background-color: #28a745; }
        .bg-yellow { background-color: #ffc107; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><i class="fas fa-trophy"></i> Admin Tournoi</div>
    <a href="tournoi_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin_publication.php"><i class="fas fa-newspaper"></i> Gérer les Publications</a>
    <a href="admin_matchs.php"><i class="fas fa-futbol"></i> Gérer les Matchs</a>
    <hr class="text-white">
    <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<!-- Contenu principal -->
<div class="content">
    <h2 class="text-center mb-4">Tableau de Bord - Admin Tournoi</h2>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="card stat-card bg-blue">
                <div class="card-body">
                    <i class="fas fa-newspaper stat-icon"></i>
                    <h5 class="card-title">Publications</h5>
                    <p class="fs-3"><?= $nb_publications; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-green">
                <div class="card-body">
                    <i class="fas fa-futbol stat-icon"></i>
                    <h5 class="card-title">Matchs</h5>
                    <p class="fs-3"><?= $nb_matchs; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-yellow">
                <div class="card-body">
                    <i class="fas fa-users stat-icon"></i>
                    <h5 class="card-title">Équipes</h5>
                    <p class="fs-3"><?= $nb_equipes; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
