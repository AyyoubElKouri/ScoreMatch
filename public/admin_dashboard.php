<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_global') {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

// Récupérer les statistiques
$nb_equipes = $pdo->query("SELECT COUNT(*) FROM equipes")->fetchColumn();
$nb_matchs = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();
$nb_staff = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
$nb_joueurs = $pdo->query("SELECT COUNT(*) FROM joueurs")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Global - Dashboard</title>
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
            transition: 0.3s;
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
            transition: 0.3s;
        }

        /* Styles pour les cartes statistiques */
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
        /* Sidebar avec scroll */
.sidebar {
    height: 100vh; /* 100% de la hauteur de l'écran */
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #212529;
    padding-top: 20px;
    overflow-y: auto; /* Active le scroll vertical */
    scrollbar-width: thin; /* Scroll fin sur Firefox */
    scrollbar-color: #888 #212529; /* Couleur du scroll */
}

/* Style du scroll pour Chrome, Edge et Safari */
.sidebar::-webkit-scrollbar {
    width: 8px; /* Largeur du scroll */
}
.sidebar::-webkit-scrollbar-thumb {
    background: #888; /* Couleur de la barre de défilement */
    border-radius: 10px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: #555; /* Couleur au survol */
}

        .bg-blue { background-color: #007bff; }
        .bg-green { background-color: #28a745; }
        .bg-yellow { background-color: #ffc107; }
        .bg-red { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><i class="fas fa-futbol"></i> Admin Global</div>
    <a href="admin_equipes.php"><i class="fas fa-users"></i> Gérer les Équipes</a>

    <a href="admin_staff.php"><i class="fas fa-user-tie"></i> Gérer le Staff</a>
    <a href="admin_joueurs.php"><i class="fas fa-user"></i> Gérer les Joueurs</a>
    <a href="admin_arbitres.php"><i class="fas fa-trophy"></i> Gérer les arbites</a>
    <a href="admin_compte.php"><i class="fas fa-user-shield"></i> Gérer les Admins Tournoi</a>

    <hr class="text-white">
    <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Se Déconnecter</a>
</div>



<!-- Contenu principal -->
<div class="content">
    <h2 class="text-center mb-4">Tableau de Bord - Admin Global</h2>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card bg-blue">
                <div class="card-body">
                    <i class="fas fa-users stat-icon"></i>
                    <h5 class="card-title">Équipes</h5>
                    <p class="fs-3"><?= $nb_equipes; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-green">
                <div class="card-body">
                    <i class="fas fa-futbol stat-icon"></i>
                    <h5 class="card-title">Matchs</h5>
                    <p class="fs-3"><?= $nb_matchs; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-yellow">
                <div class="card-body">
                    <i class="fas fa-user-tie stat-icon"></i>
                    <h5 class="card-title">Staff</h5>
                    <p class="fs-3"><?= $nb_staff; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-red">
                <div class="card-body">
                    <i class="fas fa-user stat-icon"></i>
                    <h5 class="card-title">Joueurs</h5>
                    <p class="fs-3"><?= $nb_joueurs; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function updateMatchCount() {
        fetch("get_match_count.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("match-count").innerText = data.count;
            })
            .catch(error => console.error("Erreur lors de la récupération des matchs :", error));
    }

    // Mettre à jour toutes les 5 secondes
    setInterval(updateMatchCount, 5000);
</script>

</body>
</html>
